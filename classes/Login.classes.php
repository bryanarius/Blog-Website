<?php

class Login extends Dbh{

    protected function getUser($uid, $pwd) {
        $sql = "SELECT pwd FROM oopusers WHERE username = ? OR email = ?";

        $sql1 = "SELECT * FROM oopusers WHERE username = ? OR email = ? AND pwd = ?";
        $stmt= $this->connect()->prepare($sql);

        if(!$stmt->execute(array($uid,$pwd))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        if($stmt->rowCount() == 0) {
            $stmt = null;
            header("location: ../index.php?error=usernotfound");
            exit();
        }

        $pwdHashed = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $checkPwd = password_verify($pwd, $pwdHashed[0]["pwd"]);

        if($checkPwd == false) {
            $stmt = null;
            header("location: ../index.php?error=usernotfound");
            exit();
        } elseif($checkPwd == true){
            $stmt= $this->connect()->prepare($sql1);

            if(!$stmt->execute(array($uid, $uid, $pwd))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }

            if($stmt->rowCount() == 0) {
                $stmt = null;
                header("location: ../index.php?error=usernotfound");
                exit();
            }

            $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

            session_start();
            $_SESSION["userid"] = $user[0]["id"];
            $_SESSION["useruid"] = $user[0]["username"];
        }

        $stmt = null;

    }

}