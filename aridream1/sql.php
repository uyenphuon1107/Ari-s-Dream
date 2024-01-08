<?php

/**
 * queries to create 2 tables:
 * create table users (id int auto_increment primary key, username varchar(255) unique key, name varchar(255) not null, password varchar(255) not null);  
 * create table user_images (id int auto_increment primary key, poem varchar(255), image longblob, username varchar(255), favorite boolean DEFAULT 0,foreign key (username) references users (username));
 * SET GLOBAL max_allowed_packet=67108864;
 */
require_once 'db.php';

function getConnection()
{
    global $hn, $un, $pw, $db;
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_errno) {
        echo "Failed to connect to MySQL" . $conn->connect_error;
        exit();
    }

    return $conn;
}


function getHash($string)
{
    $salt1 = '!@#@!$!@#';
    $salt2 = 'secretsalt';
    $token = hash('ripemd128', "$salt1$string$salt2");

    return $token;
}


function login($username, $password)
{
    $conn = getConnection();
    $hashPassword = getHash($password);

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->bind_param("ss", $username, $hashPassword);
        $stmt->execute();
        $result = $stmt->get_result();
        $returnValue = ($result->num_rows > 0);
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}


function register($name, $username, $password)
{
    $conn = getConnection();
    $hashPassword = getHash($password);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, username, password) VALUES (?,?,?)");
        $stmt->bind_param('sss', $name, $username, $hashPassword);

        $stmt->execute();

        $result = $stmt->affected_rows;
        $returnValue = ($result > 0);
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}


function usernameIsDuplicated($username)
{
    $conn = getConnection();

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param('s', $username);

        $stmt->execute();

        $result = $stmt->get_result();
        $returnValue = ($result->num_rows > 0);
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}


function getImagesByUsername($username)
{
    $conn = getConnection();

    try {
        $stmt = $conn->prepare("SELECT * FROM user_images WHERE username=?");
        $stmt->bind_param('s', $username);

        $stmt->execute();

        $result = $stmt->get_result();
        $images = array();
        while ($row = $result->fetch_assoc()) {
            array_push($images, [
                'id' => $row['id'],
                'poem' => $row['poem'],
                'image' => $row['image'],
                'favorite' => $row['favorite']
            ]);
        }
        $returnValue = $images;
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}


function storeData($poem, $imageContent, $username)
{
    $conn = getConnection();

    // Set max_allowed_packet to 10MB
    $sql = "SET GLOBAL max_allowed_packet=10485760";

    try {
        $conn->query($sql);
        $stmt = $conn->prepare("INSERT INTO user_images (poem, image, username) VALUES (?, ?, ?)");

        $stmt->bind_param('sss', $poem, $imageContent, $username);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $returnValue = ($result > 0);
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}


function deleteData($id)
{

    $conn = getConnection();

    try {
        $stmt = $conn->prepare("DELETE FROM user_images WHERE id=?");

        $stmt->bind_param('i', $id);

        $stmt->execute();

        $result = $stmt->affected_rows;

        $returnValue = ($result > 0);
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}

function toggleFavorite($id)
{
    $conn = getConnection();

    try {
        $stmt = $conn->prepare("UPDATE user_images SET favorite = NOT favorite WHERE id=?");

        $stmt->bind_param('i', $id);

        $stmt->execute();

        $result = $stmt->affected_rows;

        $returnValue = ($result > 0);
        $stmt->close();
    } catch (Exception $e) {
        $returnValue = $e->getMessage();
    }

    $conn->close();

    return $returnValue;
}
