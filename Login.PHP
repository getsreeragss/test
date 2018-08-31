<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function login()
    {
        $this->load->model('modelName');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->input->post('email');
            $password = $this->input->post('password');
            if ($username == '') {
                $response = array(
                    "status" => "Failure",
                    "message" => "Email Id Required"
                );
            } else if ($password == '') {
                $response = array(
                    "status" => "Failure",
                    "message" => "Password Required"
                );
            } else {
                $details = $this->modelName->login(array("u.email_id" => $username,"password"=> $password));
                if ($details) {
                    $response = array(
                        "status" => "Success",
                        "user_id" => $details['user_id'],
                        "fname" => $details['fname'],
                        "lname" => $details['lname'],
                        "token" => $details['token'],
                        "email" => $details['email_id'],
                    );

                }else{
                    $response = array(
                        "status" => "Failure",
                        "message" => "Invalid User Crdentials"
                    );
                }
            }
            echo json_encode($response);
        }    
    }
    public function forgot_password() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $from_email = APP_MAIL;
            $from_name = APP_MAIL_NAME;
            $subject = 'Forgot Password';
            $this->load->model('modelName'); 
            $email_id = $this->input->post('email');
            if ($email_id == '') {
                $response = array(
                    "status" => "Failure",
                    "message" => "Email Id Required"
                );
            } else {
                $user = $this->modelName->getuser(array("email_id" => $email_id));
                
                if (!empty($user)) {
                     
                   
                    $data = array(
                        "user_id" => $user['user_id'],
                        "created_date" => date("Y-m-d H:i:s"),
                        "status" => 0
                    );
                    $inserted_id = $this->ModelName->insert_forgot_password($data);
                    if ($inserted_id) {
                         
                                $fp_id = $this->my_encrypt->encode($inserted_id);
                                $to = $user["email_id"];

                                $this->load->library('parser');
                                $link = base_url() . "auth/reset-userpassword/" . $fp_id;

                                $template_values = array(
                                    'user' => $user["fname"] . " " . $user["lname"],
                                    'reset_link' => $link,
                                    'email' => $to
                                );
                                $this->load->model('companys');
                                
                                $subject = "Forgot password";
                                $body ="Body";

                                $message ="Message to the user";

                                $email = array(
                                    'html' => $message, //Consider using a view file
                                    'subject' => $subject,
                                    'from_email' => "no-reply@yopmail.com",
                                    'from_name' => "Admin",
                                    'to' => array(array('email' => $to))
                                );
                                $this->load->library('mandrill');
                                $result = $this->mandrill->messages->send($email);
                                if (($result[0]["status"] == "rejected") || ($result[0]["status"] == "invalid")) {
                                    $response = array("status" => "Failure", "message" => "Invalid Email Id");
                                } else {
                                    $response = array(
                                        "status" => "Success",
                                        "message" => "An Email sent to " . $email_id . " with the login credentials"
                                    );
                                }
                            }
                        }else {
                            $response = array(
                                "status" => "Failure",
                                "message" => "Invalid Email Id"
                            );
                        }
                    }    
            echo json_encode($response);
        }
    }

    public function change_user_password()
    {
        $this->load->model('modelName');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_token = $_POST['token'];
            $response = array("status" => "Failure");
            if (empty($user_token)) {
                $response = array(
                    "status" => "Failure",
                    "message" => "User Token is Required"
                );
            } else {
                $user_id = $this->ModelName->get_user_id($user_token);
                $where = array('token' => $user_token, 'user_id' => $user_id);
                if ($user_id) {
                    if (!empty($_POST['password'])) {
                        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                        $data = array(
                            'password' => $hash
                        );
                    }

                    $upd = $this->ModelName->update_user($data, $where);
                    if ($upd) {
                        $where = array('token' => $user_token);
                        $details = $this->ModelName->get_user($where);
                        if (!empty($details)) {
                            $response = array(
                                "status" => "Success",
                                "profile" => $details[0]
                            );
                        } else {
                            $response = array(
                                "status" => "Success",
                                "profile" => null
                            );
                        }
                    }
                } else {
                    $response = array(
                        "status" => "Failure",
                        "message" => "User Token not valid"
                    );
                }
            }
            echo json_encode($response);
        }
    }

}
?>
