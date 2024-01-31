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

    // public function create_Image($imageData = null)
    // {
    //     // If Blob data is provided, save it directly
    //     if (!empty($imageData)) {
    //         echo 'base64' . $imageData;
    //         // Generate a unique file name
    //         $new_name = time() . date('YmdHis') . '.jpg'; // You can change the extension as needed
    //         $file_path = 'uploads/' . $new_name;

    //         // Decode the base64 data
    //         $decodedData = base64_decode($imageData);

    //         // Save the Blob data to a file
    //         if (file_put_contents($file_path, $decodedData)) {
    //             return base_url($file_path);
    //         }

    //         return false;
    //     }

    //     // If no Blob data, handle file upload as before
    //     $upload_path = 'uploads/';
    //     $config['upload_path'] = $upload_path;
    //     $config['allowed_types'] = 'jpg|png';
    //     $config['max_size'] = 4098;
    //     $new_name = time() . date('YmdHis');
    //     $config['file_name'] = $new_name;
    //     $this->load->library('upload', $config);

    //     if ($this->upload->do_upload('image')) {
    //         $data = $this->upload->data();
    //         $file_name = $data['file_name'];
    //         return base_url('uploads/' . $file_name);
    //     }

    //     return false;
    // }

    public function create_Image($imageData = null)
    {
        try {
            if (!empty($imageData)) {
                // If Blob data is provided, save it directly
                $decodedData = base64_decode($imageData);
                // Load the image manipulation library
                $this->load->library('image_lib');

                // Generate a unique file name
                $new_name = time() . date('YmdHis') . '.jpg'; // Change the extension if needed
                $file_path = 'uploads/' . $new_name;

                // Save the Blob data to a file
                if (file_put_contents($file_path, $decodedData)) {
                    // Compress the image
                    $this->compressImage($file_path, 360);

                    return base_url($file_path);
                }

                return false;
            }

            // If no Blob data, handle file upload as before
            $upload_path = 'uploads/';
            $config['upload_path'] = $upload_path;
            // $new_name = time() . date('YmdHis') . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            // $config['file_name'] = $new_name;
            // $config['allowed_types'] = '*'; // Add additional allowed formats if needed
            $config['allowed_types'] = '*'; // Add additional allowed formats if needed
            $config['max_size'] = 32500;
            // $new_name = time() . date('YmdHis');

            // $contentTypeHeader = $this->input->get_request_header('Content-Type');
            // $extension = explode('/', $contentTypeHeader)[1];

            $new_name = time() . date('YmdHis') . '.jpg';
            $config['file_name'] = $new_name;

            // $config['file_name'] = $new_name;
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('image')) {
                $data = $this->upload->data();
                $file_path = $upload_path . $data['file_name'];

                // Compress the uploaded image
                $this->compressImage($file_path, 360);

                // return base_url('uploads/' . $data['file_name']);
                return 'https://kajahapi.infantsurya.in/uploads/' . $data['file_name'];
            }

            throw new \Exception('Image Upload Failed');
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
        }
    }

    // Function to compress the image to a specified width
    private function compressImage($file_path, $max_width)
    {
        $config['image_library'] = 'gd2';
        $config['source_image'] = $file_path;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = $max_width;
        $config['file_ext_tolower'] = TRUE;

        $this->image_lib->initialize($config);
        $this->image_lib->resize();
        $this->image_lib->clear();
    }
}
