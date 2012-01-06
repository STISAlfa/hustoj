<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Account extends Controller_My{

    public function action_register()
    {
        $this->view->title = "User Register";

        $body = View::factory('/user/register');

        $this->view->body = $body;
    }

    public function action_profile()
    {

    }

    public function action_setting()
    {
        $request = $this->request;
        // need login
        $uid = Auth::instance()->get_user();
        if ($uid == null) $request->redirect('/home');

        if ($request->method() == 'GET')
        {
            $u = new Model_User();
            $user = $u->get_info_by_name($uid);
        } else {
            // if POST then update user info
            $user = $request->post();
            $user['user_id'] = $uid;

            $u = New Model_User();
            // check user password
            if (Auth::instance()->check_password($user['password']))
            {
                // if change password
                if ( strlen($user['newpassword']) > 0
                    AND ($user['newpassword'] === $user['confirm']))
                {
                    $user['password'] = $user['newpassword'];
                }
                //TODO: Validation user input, see action_new
                $ret = $u->update_information($user);
                $tip = 'Update Success';
            } else {
                $error = 'Password Wrong';
            }
        }

        $body = View::factory('user/edit');
        $body->userinfo = $user;
        $body->error = isset($error)? $error: null;
        $body->tip = isset($tip)? $tip: null;

        $this->view->title = "Update Imformation";
        $this->view->body = $body;
   	}


    public function action_new()
    {
        if ($this->request->method() == 'GET')
        {
            $this->request->redirect('/home');
        }

        $post = Validation::factory($this->request->post())
            ->rule('username', 'not_empty')
            ->rule('username', 'min_length', array(':value', 4))
            ->rule('username', 'max_length', array(':value', 15))
            ->rule('username', 'alpha_numeric')
            //->rule('username', 'User_Model::unique_username')
            ->rule('password', 'min_length', array(':value', 6))
            ->rule('password', 'matches', array(':validation', 'password', 'confirm'))
            ->rule('school', 'max_length', array(':value', 30))
            ->rule('email', 'max_length', array(':value', 30))
            ->rule('email', 'email');

        $this->view->title = 'test';
        if($post->check())
        {
            $this->view->title = 'ok';
            //todo:user add
            //todo: redirect new user page
        }
        $this->view->body = Debug::dump($post->errors());

    }
}