
 
 <?php
    defined('BASEPATH') or exit('No direct script access allowed');

    class Email_model extends CI_Model
    {





        public function send_email($data)
        {
            $from_name         = 'Kajah Tea';
            $from_mail         = 'test@faheemas.sg';
            $this->load->library('email');
            $config = array(
                'protocol'  => 'smtp',
                'smtp_host' => 'mail.faheemas.sg',
                'smtp_port' => 587,
                'smtp_user' => 'test@faheemas.sg',
                'smtp_pass' => 'MyTesting@123',
                'mailtype'  => 'html',
                'charset'   => 'utf-8',
                'wordwrap' => TRUE
            );

            $this->email->initialize($config);
            $message = $this->load->view($data['template_path'], $data, TRUE);
            $this->email->from($from_mail, $from_name);
            $this->email->to($data['to']);
            $this->email->subject($data['subject']);
            $this->email->message($message);
            $this->email->set_newline("\r\n");

            if ($this->email->send()) {
                return true;
            } else {
                print_r($this->email->print_debugger(array('headers')));
                die();
                //$this->session->set_flashdata('error', $this->email->print_debugger(array('headers')));
                $this->session->set_flashdata('error', 'Mail cant send to the recipient');
                return false;
            }
        }

        public function get_user($user_id, $type)
        {
            $this->db->select('*');
            $this->db->from($type);
            $this->db->where('id', $user_id);
            $query = $this->db->get();
            if ($query->num_rows() != 0) {
                $result = $query->row();
                return $result;
            } else return false;
        }

        public function send_email_reset_password($user_id, $type)
        {

            $user = $this->get_user($user_id, $type);
            if (!empty($user)) {
                $token = $user->token;
                if (empty($token)) {
                    $token = generate_token();
                    $data = array(
                        'token' => $token
                    );
                    $this->db->where('id', $user->id);
                    $this->db->update($type, $data);
                }
                $email = $type . 'Email';
                $info = [
                    'token' => $token,
                    'type' => $type
                ];
                $data = array(
                    'subject' => "Reset Password",
                    'to' => $user->$email,
                    'template_path' => "email/email_reset_password",
                    'info' => $info
                );

                $this->send_email($data);
            }
        }
    }
