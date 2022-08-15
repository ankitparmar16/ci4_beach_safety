<?php

namespace App\Controllers;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\Users;
use App\Models\HomeModel;
use App\Models\Import;

/**
 * @property IncomingRequest $request 
 */

class Attendance extends BaseController
{
    public $userModel;
    public function __construct()
    {   
        $this->userModel = new Users();
        $this->homeModel = new HomeModel();
        $this->importModel = new Import();
        $this->session = \Config\Services::session();
    }

    public function index()
    {   
        if(!session()->has('logged_user')) {
            return redirect()->to("./auth/login");
        }
        
        $data = array(
            'title' => 'Shama | Attendance'
        );

        return view('attendance_view', $data);
    }

    public function import_attendance()
    {
        if(!session()->has('logged_user')) {
            return redirect()->to("./auth/login");
        }
        
        if ($this->request->getMethod() == 'post') {
			if ($this->request->getFile('atnd_file') !== null) {

				$file = $this->request->getFile('atnd_file');

                $type = $file->getMimeType();
                $size = $file->getSize();

                if($type == 'application/vnd.ms-excel' || $type == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    if($size <= 5000000) {
                        if($this->importModel->upload($file)){
                            $this->session->setTempdata('success', 'Upload Successful!');
                            return redirect()->to(base_url().'/attendance');
                        } else {
                            $this->session->setTempdata('error', 'Something went wrong!');
                            return redirect()->to(base_url().'/attendance');
                        }

                    } else {
                        $this->session->setTempdata('error', 'File size should be less than 5 MB!');
                        return redirect()->to(base_url().'/attendance');
                    }
                } else {
                    $this->session->setTempdata('error', 'Invalid File!');
                    return redirect()->to(base_url().'/attendance');
                }
			}
		} else {
            $this->session->setTempdata('error', 'Something went wrong!');
            return redirect()->to(base_url().'/attendance');
        }
    }

    public function _remap($method, $param = null)
    {
        if (method_exists($this, $method)) {
            return $this->$method($param);
        }
        throw PageNotFoundException::forPageNotFound();
    }
}