<?php

namespace Matcha\Lib;

use PDO;

class Database
{
    private $db_dsn;
    private $db_user;
    private $db_pass;
    private $pdo;

    public function __construct($db_dsn, $db_user, $db_pass)
    {
        $this->db_dsn  = $db_dsn;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
    }

    private function getPDO()
    {
        if ($this->pdo === null) {
            $pdo = new PDO($this->db_dsn, $this->db_user, $this->db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo = $pdo;
        }
        return ($this->pdo);
    }

    public function query($request, $fetch = false, $one = false, $count = false)
    {
        try {
            $req = $this->getPDO()->query($request);
            if ($req) {
                if ($fetch) {
                    if ($count)
                        $data = $req->rowCount();
                    elseif ($one)
                        $data = $req->fetch(PDO::FETCH_OBJ);
                    else
                        $data = $req->fetchAll(PDO::FETCH_OBJ);
                    $req->closeCursor();
                    return ($data);
                }
            }
            return ($req);
        } catch (\PDOException $e) {
            exit($e->getMessage() . "<br>File: " . $e->getFile() . "<br>Line: " . $e->getLine());
        }
    }

    public function prepare($request, $attributes, $fetch = false, $one = false, $count = false)
    {
        try {
            $req = $this->getPDO()->prepare($request);
            $exec = $req->execute($attributes);
            if ($exec) {
                if ($fetch) {
                    if ($count)
                        $data = $req->rowCount();
                    elseif ($one)
                        $data = $req->fetch(PDO::FETCH_OBJ);
                    else
                        $data = $req->fetchAll(PDO::FETCH_OBJ);
                    $req->closeCursor();
                    return ($data);
                }
            }
            return ($exec);
        } catch (\PDOException $e) {
            exit($e->getMessage() . "<br>File: " . $e->getFile() . "<br>Line: " . $e->getLine());
        }
    }
}
