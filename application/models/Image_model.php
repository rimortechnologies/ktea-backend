<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Image_model extends CI_Model
{




    // public function create_Image()
    // {
    //     $upload_path = 'uploads/';
    //     $config['upload_path'] = $upload_path;
    //     $config['allowed_types'] = 'jpg|png';
    //     $config['max_size'] = 2048;
    //     $new_name = time() . date('YmdHis');
    //     $config['file_name'] = $new_name;
    //     $this->load->library('upload', $config);
    //     if ($this->upload->do_upload('image')) {
    //         $data = $this->upload->data();
    //         $file_name = $data['file_name'];
    //         return base_url('uploads/' . $file_name);
    //     }
    //     return FALSE;
    // }

    // public function create_Image($imageData = null)
    // {
    //     // If Blob data is provided, save it directly
    //     if (!empty($imageData)) {
    //         // Generate a unique file name
    //         $new_name = time() . date('YmdHis') . '.jpg'; // You can change the extension as needed
    //         $file_path = 'uploads/' . $new_name;

    //         // Save the Blob data to a file
    //         if (file_put_contents($file_path, $imageData)) {
    //             return base_url($file_path);
    //         }

    //         return FALSE;
    //     }

    //     // If no Blob data, handle file upload as before
    //     $upload_path = 'uploads/';
    //     $config['upload_path'] = $upload_path;
    //     $config['allowed_types'] = 'jpg|png';
    //     $config['max_size'] = 2048;
    //     $new_name = time() . date('YmdHis');
    //     $config['file_name'] = $new_name;
    //     $this->load->library('upload', $config);

    //     if ($this->upload->do_upload('image')) {
    //         $data = $this->upload->data();
    //         $file_name = $data['file_name'];
    //         return base_url('uploads/' . $file_name);
    //     }

    //     return FALSE;
    // }

    public function create_Image($imageData = null)
    {
        // If Blob data is provided, save it directly
        if (!empty($imageData)) {
            echo 'base64' . $imageData;
            // Generate a unique file name
            $new_name = time() . date('YmdHis') . '.jpg'; // You can change the extension as needed
            $file_path = 'uploads/' . $new_name;

            // Decode the base64 data
            $decodedData = base64_decode($imageData);

            // Save the Blob data to a file
            if (file_put_contents($file_path, $decodedData)) {
                return base_url($file_path);
            }

            return false;
        }

        // If no Blob data, handle file upload as before
        $upload_path = 'uploads/';
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|png';
        $config['max_size'] = 4098;
        $new_name = time() . date('YmdHis');
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('image')) {
            $data = $this->upload->data();
            $file_name = $data['file_name'];
            return base_url('uploads/' . $file_name);
        }

        return false;
    }
}
