<?php

namespace App;


class Authorization
{
    private Database $database;
    private Session $session;

    /**
     * Authorization constructor.
     * @param Database $database
     * @param Session $session
     */
    public function __construct(Database $database, Session $session)
    {
        $this->database = $database;
        $this->session = $session;
    }

    public function register(array $data): bool
    {
        if (empty($data['username'])) {
            throw new AuthorizationException('The username should not be empty');
        }

        if (empty($data['email'])) {
            throw new AuthorizationException('The email should not be empty');
        }

        if (empty($data['password'])) {
            throw new AuthorizationException('The password should not be empty');
        }

        if ($data['password'] !== $data['confirm_password']) {
            throw new AuthorizationException('The password and confirm password should match');
        }

        /**
         * validate email
         */
        $statement = $this->database->getConnection()->prepare(
            'SELECT * FROM user WHERE email = :email'
        );
        $statement->execute([
            'email' => $data['email']
        ]);
        $user = $statement->fetch();
        if(!empty($user)) {
           throw new AuthorizationException('User with such email exists');
        }

        /**
         * validate username
         */
        $statement = $this->database->getConnection()->prepare(
            'SELECT * FROM user WHERE username = :username'
        );
        $statement->execute([
            'username' => $data['username']
        ]);
        $user = $statement->fetch();
        if(!empty($user)) {
            throw new AuthorizationException('User with such username exists');
        }

        $statement = $this->database->getConnection()->prepare(
            'INSERT INTO user (email, username, password) VALUES (:email, :username, :password)'
        );

        $statement->execute([
            'email'    => $data['email'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT)
        ]);

        return true;
    }

    /**
     * @param string $email
     * @param $password
     * @return bool
     * @throws AuthorizationException
     */
    public function login(string $email, $password): bool {
        if (empty($email)) {
            throw new AuthorizationException('The email should not be empty');
        }
        if (empty($password)) {
            throw new AuthorizationException('The password should not be empty');
        }

        $statement = $this->database->getConnection()->prepare(
            'SELECT * FROM user WHERE email = :email'
        );
        $statement->execute([
            'email' => $email
        ]);

        $user = $statement->fetch();
        if(empty($user)) {
            throw new AuthorizationException('User with such email not found1');
        }

        if(password_verify($password, $user['password'])) {
            $this->session->setData('user', [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]);
            return true;
        }

        throw new AuthorizationException('Incorrect email or password');

    }

}