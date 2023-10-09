<?php

namespace App\Controllers;
use CodeIgniter\Controllers;
use App\Models\M_model;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Home extends BaseController
{
    public function index()
    {
        if(session()->get('id')>0 ) {
            return redirect()->to('home/dashboard');

        }else{

            $model=new M_model();
            echo view('login');
        }
    }

    public function aksi_login()
    {
        $n=$this->request->getPost('username'); 
        $p=$this->request->getPost('password');

        $captchaResponse = $this->request->getPost('g-recaptcha-response');
        $captchaSecretKey = '6Le4D6snAAAAAHD3_8OPnw4teaKXWZdefSyXn4H3';

        $verifyCaptchaResponse = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret={$captchaSecretKey}&response={$captchaResponse}"
        );

        $captchaData = json_decode($verifyCaptchaResponse);

        if (!$captchaData->success) {

            session()->setFlashdata('error', 'CAPTCHA verification failed. Please try again.');
            return redirect()->to('/Home');
        }

        $model= new M_model();
        $data=array(
            'username'=>$n, 
            'password'=>md5($p)
        );
        $cek=$model->getarray('user', $data);
        if ($cek>0) {
            $where=array('id_user_petugas'=>$cek['id_user']);
            $pengguna=$model->getarray('petugas', $where);
            session()->set('id', $cek['id_user']);
            session()->set('username', $cek['username']);

            session()->set('nama_petugas', $pengguna['nama_petugas']);

            session()->set('level', $cek['level']);
            return redirect()->to('/home/dashboard');

        }else {
            return redirect()->to('/');
        }
    }


    public function log_out()
    {
        if(session()->get('id')>0) {

           session()->destroy();
           return redirect()->to('/');

       }else{
        return redirect()->to('/home/dashboard');
    }
}

public function ganti_profile()
{
    if(session()->get('level')== 1 || session()->get('level')== 2 || session()->get('level')== 3) {

        $id=session()->get('id');
        $where2=array('id_user'=>$id);
        $where=array('id_user_petugas'=>$id);
        $model=new M_model();
        $pakif['users']=$model->edit_pp('petugas',$where);
        $pakif['use']=$model->edit_pp('user',$where2);

        $kui['foto']=$model->getRow('user',$where2);

        $id=session()->get('id');


        echo view('header',$kui);
        echo view('menu');
        echo view('ganti_pp', $pakif);
        echo view('footer');
    }else {
        return redirect()->to('/home/dashboard');
    }
}

public function aksi_ganti_profile()
{
    $model= new M_model();
    $id=session()->get('id');
    $where=array('id_user'=>$id);
    $photo=$this->request->getFile('foto');
    $kui=$model->getRow('user',$where);
    if( $photo != '' ){}
        elseif($photo != '' && file_exists(PUBLIC_PATH."/images/profile/".$kui->foto) ) 
        {
            unlink(PUBLIC_PATH."/images/profile/".$kui->foto);
        }
        elseif($photo == '')
        {
            $username= $this->request->getPost('username');
            $nip= $this->request->getPost('nip');   
            $nama_petugas= $this->request->getPost('nama_petugas');  
            $jk= $this->request->getPost('jk');          
            $alamat= $this->request->getPost('alamat');    
            $telp= $this->request->getPost('telp');                     

            $user=array(
                'username'=>$username,
            );
            $model->edit('user', $user,$where);
            $where2=array('id_user_petugas'=>$id);

            $pegawai=array(
                'nip'=>$nip,
                'nama_petugas'=>$nama_petugas,
                'jk'=>$jk,
                'alamat'=>$alamat,
                'telp'=>$telp,
            );
            $model->edit('petugas', $pegawai, $where2);
            return redirect()->to('/home/ganti_profile');
        }

        $username= $this->request->getPost('username');
        $nip= $this->request->getPost('nip');   
        $nama_petugas= $this->request->getPost('nama_petugas');  
        $jk= $this->request->getPost('jk');          
        $alamat= $this->request->getPost('alamat');    
        $telp= $this->request->getPost('telp');    

        $img = $photo->getRandomName();
        $photo->move(PUBLIC_PATH.'/images/profile/',$img);
        $user=array(
            'username'=>$username,
            'foto'=>$img
        );
        $model=new M_model();
        $model->edit('user', $user,$where);

        $pegawai=array(
            'nip'=>$nip,
            'nama_petugas'=>$nama_petugas,
            'jk'=>$jk,
            'alamat'=>$alamat,
            'telp'=>$telp,
        );
        $where2=array('id_user_petugas'=>$id);
        $model->edit('petugas', $pegawai, $where2);
        return redirect()->to('/home/ganti_profile');
    }


    public function ganti_password()  
    {
        if(session()->get('level')== 1 || session()->get('level')== 2 || session()->get('level')== 3) {

            $id=session()->get('id');
            $where2=array('id_user'=>$id);
            $model=new M_model();
            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);
            $pakif['use']=$model->getRow('user',$where2);

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            echo view('header',$kui);
            echo view('menu');
            echo view('ganti_pw',$pakif);
            echo view('footer');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_ganti_pw()   
    {
        $pass=$this->request->getPost('password');
        $id=session()->get('id');
        $model= new M_model();

        $data=array( 
            'password'=>md5($pass)
        );

        $where=array('id_user'=>$id);
        $model->edit('user', $data, $where);

        return redirect()->to('/home/log_out');
    }

    public function dashboard()
    {
        if(session()->get('id')>0) {

            $model= new M_model();
            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);       

            echo view('header', $kui);
            echo view('menu');
            echo view('dashboard');
            echo view('footer');
        }else{
            return redirect()->to('/');
        }
    }

    public function pegawaian()
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $on='petugas.id_user_petugas=user.id_user';
            $kui['jofinson']=$model->tampilPegawaian('petugas', 'user', $on, 'tanggal_petugas');

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('pegawaian/pegawaian');
            echo view('footer'); 
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function tambah_pegawaian()
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $kui['jofinson']=$model->tampil('petugas');

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('pegawaian/tambah_pegawaian');
            echo view('footer');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_tambah_pegawaian()
    {
        $model=new M_model();

        $nip=$this->request->getPost('nip');
        $nama_petugas=$this->request->getPost('nama_petugas');
        $jk=$this->request->getPost('jk');
        $alamat=$this->request->getPost('alamat');
        $telp=$this->request->getPost('telp');
        $username=$this->request->getPost('username');
        $password=$this->request->getPost('password');
        $level=$this->request->getPost('level');

        $user=array(
            'username'=>$username,
            'password'=>md5('Jofinson123'),
            'level'=>$level,
        );

        $model=new M_model();
        $model->simpan('user', $user);
        $where=array('username'=>$username);
        $id=$model->getarray('user', $where);
        $iduser = $id['id_user'];

        $pegawai=array(
            'nip'=>$nip,
            'nama_petugas'=>$nama_petugas,
            'jk'=>$jk,
            'alamat'=>$alamat,
            'telp'=>$telp,
            'id_user_petugas'=>$iduser,
        );
        $model->simpan('petugas', $pegawai);
        return redirect()->to('/home/pegawaian');
    }

    public function reset_pw($id)
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $where=array('id_user'=>$id);
            $data=array(
                'password'=>md5('Jofinson123')
            );
            $model->edit('user',$data,$where);
            return redirect()->to('/home/pegawaian');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function edit_pegawaian($id)
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $where2=array('petugas.id_user_petugas'=>$id);
            $on='petugas.id_user_petugas=user.id_user';
            $kui['jofinson']=$model->edit_petugas('petugas', 'user',$on, $where2);

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('pegawaian/edit_pegawaian');
            echo view('footer');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_edit_pegawaian()
    {
        $id= $this->request->getPost('id');
        $nip=$this->request->getPost('nip');
        $nama_petugas=$this->request->getPost('nama_petugas');
        $jk=$this->request->getPost('jk');
        $alamat=$this->request->getPost('alamat');
        $telp=$this->request->getPost('telp');
        $username=$this->request->getPost('username');
        $password=$this->request->getPost('password');
        $level=$this->request->getPost('level');

        $where=array('id_user'=>$id);    
        $where2=array('id_user_petugas'=>$id);
        if ($password !='') {
            $user=array(
                'username'=>$username,
                'level'=>$level,
            );
        }else{
            $user=array(
                'username'=>$username,
                'level'=>$level,
            );
        }

        $model=new M_model();
        $model->edit('user', $user,$where);

        $pegawai=array(
            'nip'=>$nip,
            'nama_petugas'=>$nama_petugas,
            'jk'=>$jk,
            'alamat'=>$alamat,
            'telp'=>$telp,
        );
        $model->edit('petugas', $pegawai, $where2);
        return redirect()->to('/home/pegawaian');
    }

    public function hapus_pegawaian($id)
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $where2=array('id_user'=>$id);
            $where=array('id_user_petugas'=>$id);
            $model->hapus('petugas',$where);
            $model->hapus('user',$where2);
            return redirect()->to('/home/pegawaian');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function jenis()
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $kui['jofinson']=$model->tampil_jenis('jenis', 'tanggal_jenis');

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('jenis/jenis');
            echo view('footer'); 
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function tambah_jenis()
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $kui['jofinson']=$model->tampil_jenis('jenis', 'tanggal_jenis');

            $id=session()->get('id');
            $where=array('id_user'=>$id);
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('jenis/tambah_jenis');
            echo view('footer'); 
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_tambah_jenis()
    {
        $model=new M_model();
        $nama_jenis=$this->request->getPost('nama_jenis');
        $kode_jenis=$this->request->getPost('kode_jenis');
        $data=array(
            'nama_jenis'=>$nama_jenis,
            'kode_jenis'=>$kode_jenis,
            'keterangan'=>'Tidak Tersedia',
        );
        $model->simpan('jenis',$data);
        return redirect()->to('/home/jenis');
    }

    public function edit_jenis($id)
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $where2=array('id_jenis'=>$id);
            $kui['jofinson']=$model->edit_jenis('jenis', $where2);

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('jenis/edit_jenis');
            echo view('footer');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_edit_jenis()
    {
        $model=new M_model();
        $id=$this->request->getPost('id');
        $nama_jenis=$this->request->getPost('nama_jenis');
        $kode_jenis=$this->request->getPost('kode_jenis');
        $data=array(
            'nama_jenis'=>$nama_jenis,
            'kode_jenis'=>$kode_jenis,
        );        
        $where=array('id_jenis'=>$id);
        $model->edit('jenis',$data,$where);
        return redirect()->to('/home/jenis');
    }

    public function hapus_jenis($id)
    {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $where=array('id_jenis'=>$id);
            $model->hapus('jenis',$where);
            return redirect()->to('/home/jenis');
        }else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function keterangan()
    {
        if (session()->get('level') == 1) {
            $ids = $this->request->getPost('jenis');

            if (is_array($ids)) {
                $model = new M_model();
                $data = array(
                    'keterangan' => "Jenis Tersedia"
                );

                foreach ($ids as $id) {
                    $where = array('id_jenis' => $id);
                    $model->edit('jenis', $data, $where);
                }

                return redirect()->to('home/jenis');
            } else {
                return redirect()->to('home/jenis')->with('error', 'Invalid input data');
            }
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function ruangan()
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $kui['jofinson']=$model->tampil_jenis('ruang', 'tanggal_ruangan');

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('ruangan/ruangan');
            echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function tambah_ruang()
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $kui['jofinson']=$model->tampil_jenis('ruang', 'tanggal_ruangan');

            $id=session()->get('id');
            $where=array('id_user'=>$id);
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('ruangan/tambah_ruang');
            echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_tambah_ruang()
    {
        $model=new M_model();
        $nama_ruang=$this->request->getPost('nama_ruang');
        $kode_ruang=$this->request->getPost('kode_ruang');
        $data=array(
            'nama_ruang'=>$nama_ruang,
            'kode_ruang'=>$kode_ruang,
            'keterangan'=>'Tidak Tersedia',
        );
        $model->simpan('ruang',$data);
        return redirect()->to('/home/ruangan');
    }

    public function edit_ruang($id)
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $where2=array('id_ruang'=>$id);
            $kui['jofinson']=$model->edit_jenis('ruang', $where2);

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('ruangan/edit_ruang');
            echo view('footer');
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_edit_ruang()
    {
        $model=new M_model();
        $id=$this->request->getPost('id');
        $nama_ruang=$this->request->getPost('nama_ruang');
        $kode_ruang=$this->request->getPost('kode_ruang');
        $data=array(
            'nama_ruang'=>$nama_ruang,
            'kode_ruang'=>$kode_ruang,
        );        
        $where=array('id_ruang'=>$id);
        $model->edit('ruang',$data,$where);
        return redirect()->to('/home/ruangan');
    }

    public function hapus_ruang($id)
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $where=array('id_ruang'=>$id);
            $model->hapus('ruang',$where);
            return redirect()->to('/home/ruangan');
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function keterangan_ruang()
    {
        if (session()->get('level') == 1) {
            $ids = $this->request->getPost('ruang');

            if (is_array($ids)) {
                $model = new M_model();
                $data = array(
                    'keterangan' => "Ruangan Tersedia"
                );

                foreach ($ids as $id) {
                    $where = array('id_ruang' => $id);
                    $model->edit('ruang', $data, $where);
                }

                return redirect()->to('home/ruangan');
            } else {
                return redirect()->to('home/ruangan')->with('error', 'Invalid input data');
            }
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function inventaris()
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $on='inventaris.id_jenis=jenis.id_jenis';
            $on2='inventaris.id_ruang=ruang.id_ruang';
            $kui['jofinson']=$model->tampilinventaris('inventaris', 'jenis', 'ruang', $on, $on2, 'tanggal_tanggal');

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('inventaris/inventaris');
            echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function tambah_inventaris()
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $on='inventaris.id_jenis=jenis.id_jenis';
            $on2='inventaris.id_ruang=ruang.id_ruang';
            $kui['jofinson']=$model->tampilinventaris('inventaris', 'jenis', 'ruang', $on, $on2, 'tanggal_tanggal');

            $id=session()->get('id');
            $where=array('id_user'=>$id);
            $kui['foto']=$model->getRow('user',$where);

            $kui['j']=$model->tampil('jenis'); 
            $kui['r']=$model->tampil('ruang'); 

            echo view('header',$kui);
            echo view('menu');
            echo view('inventaris/tambah_inventaris');
            echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_tambah_inventaris()
    {
        $model=new M_model();
        $jenis=$this->request->getPost('id_jenis');
        $ruang=$this->request->getPost('id_ruang');
        $nama=$this->request->getPost('nama');
        $kode_inventaris=$this->request->getPost('kode_inventaris');
        $kondisi=$this->request->getPost('kondisi');
        $jumlah=$this->request->getPost('jumlah');
        $data=array(
            'id_jenis'=> $jenis,
            'id_ruang'=> $ruang,
            'nama'=>$nama,
            'kode_inventaris'=>$kode_inventaris,
            'kondisi'=>$kondisi,
            'jumlah'=>$jumlah,
            'keterangan_inventaris'=>'Pengecekkan / Perbaiki',
        );
        $model->simpan('inventaris',$data);
        return redirect()->to('/home/inventaris');
    }

    public function keterangan_inventaris()
    {
        if (session()->get('level') == 1) {
            $ids = $this->request->getPost('inventaris');

            if (is_array($ids)) {
                $model = new M_model();
                $data = array(
                    'keterangan_inventaris' => "Dapat Digunakan"
                );

                foreach ($ids as $id) {
                    $where = array('id_inventaris' => $id);
                    $model->edit('inventaris', $data, $where);
                }

                return redirect()->to('home/inventaris');
            } else {
                return redirect()->to('home/inventaris')->with('error', 'Invalid input data');
            }
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function hapus_inventaris($id)
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $where=array('id_inventaris'=>$id);
            $model->hapus('inventaris',$where);
            return redirect()->to('/home/inventaris');
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function edit_inventaris($id)
    {
        if (session()->get('level') == 1) {

            $model=new M_model();
            $where2=array('id_inventaris'=>$id);
            $on='inventaris.id_jenis=jenis.id_jenis';
            $on2='inventaris.id_ruang=ruang.id_ruang';
            $kui['jofinson']=$model->edit_inventaris('inventaris', 'jenis', 'ruang', $on, $on2, $where2);
            $kui['j']=$model->tampil('jenis');
            $kui['r']=$model->tampil('ruang');

            $id=session()->get('id');
            $where=array('id_user'=>$id);

            $where=array('id_user' => session()->get('id'));
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu');
            echo view('inventaris/edit_inventaris');
            echo view('footer');
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_edit_inventaris()
    {
        $model=new M_model();
        $id=$this->request->getPost('id');
        $jenis=$this->request->getPost('id_jenis');
        $ruang=$this->request->getPost('id_ruang');
        $nama=$this->request->getPost('nama');
        $kode_inventaris=$this->request->getPost('kode_inventaris');
        $kondisi=$this->request->getPost('kondisi');
        $jumlah=$this->request->getPost('jumlah');
        $data=array(
            'id_jenis'=> $jenis,
            'id_ruang'=> $ruang,
            'nama'=>$nama,
            'kode_inventaris'=>$kode_inventaris,
            'kondisi'=>$kondisi,
            'jumlah'=>$jumlah,
        );        
        $where=array('id_inventaris'=>$id);
        $model->edit('inventaris',$data,$where);
        return redirect()->to('/home/inventaris');
    }

    public function peminjaman()
    {
        if(!session()->get('id') > 0){
            return redirect()->to('/home/dashboard');
        }

        if(session()->get('level')== 1 || session()->get('level')== 2) {
            $model=new M_model();
            $on='peminjaman.id_inventaris=inventaris.id_inventaris';
            $on2='peminjaman.pembuat_peminjaman=user.id_user';
            $kui['jofinson']=$model->tampilinventaris('peminjaman', 'inventaris', 'user', $on, $on2, 'tanggal_peminjaman');
        }

        if(session()->get('level')== 3) {
            $model=new M_model();
            $where=array('username'=>session()->get('username'));
            $on='peminjaman.id_inventaris=inventaris.id_inventaris';
            $on2='peminjaman.pembuat_peminjaman=user.id_user';
            $kui['jofinson']=$model->absen_nama('peminjaman', 'inventaris', 'user', $on, $on2, 'tanggal_peminjaman', $where);
        }

        $id=session()->get('id');
        $where=array('id_user'=>$id);

        $where=array('id_user' => session()->get('id'));
        $kui['foto']=$model->getRow('user',$where);

        echo view('header',$kui);
        echo view('menu');
        echo view('peminjaman/peminjaman');
        echo view('footer'); 
    }

    public function tambah_peminjaman()
    {
        if (session()->get('level') == 1 || session()->get('level') == 2 || session()->get('level') == 3) {

        $model=new M_model();
        $on='peminjaman.id_inventaris=inventaris.id_inventaris';
        $on2='peminjaman.pembuat_peminjaman=user.id_user';
        $kui['jofinson']=$model->tampilinventaris('peminjaman', 'inventaris', 'user', $on, $on2, 'tanggal_peminjaman');

        $id=session()->get('id');
        $where=array('id_user'=>$id);
        $kui['foto']=$model->getRow('user',$where);

        $kui['j']=$model->tampil('inventaris'); 

        echo view('header',$kui);
        echo view('menu');
        echo view('peminjaman/tambah_peminjaman');
        echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_tambah_peminjaman()
    {
        $model=new M_model();
        $inventaris=$this->request->getPost('id_inventaris');
        $jumlah_pinjam=$this->request->getPost('jumlah_pinjam');
        $tanggal_pengembalian=$this->request->getPost('tanggal_pengembalian');
        $pembuat_peminjaman = session()->get('id');
        $data=array(
            'id_inventaris'=> $inventaris,
            'jumlah_pinjam'=>$jumlah_pinjam,
            'tanggal_pengembalian' => $tanggal_pengembalian,
            'pembuat_peminjaman' => $pembuat_peminjaman,
        );
        $model->simpan('peminjaman',$data);
        return redirect()->to('/home/peminjaman');
    }

    public function hapus_peminjaman($id)
    {
        if (session()->get('level') == 1 || session()->get('level') == 2 || session()->get('level') == 3) {

        $model=new M_model();
        $where=array('id_peminjaman'=>$id);
        $model->hapus('peminjaman',$where);
        return redirect()->to('/home/peminjaman');
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function pengembalian()
    {
        if (session()->get('level') == 1 || session()->get('level') == 2) {

        $model=new M_model();
        $on='pengembalian.id_inventaris=inventaris.id_inventaris';
        $on2='pengembalian.pembuat_pengembalian=user.id_user';
        $kui['jofinson']=$model->tampilinventaris('pengembalian', 'inventaris', 'user', $on, $on2, 'tanggal_pengembalian_pengembalian');

        $id=session()->get('id');
        $where=array('id_user'=>$id);

        $where=array('id_user' => session()->get('id'));
        $kui['foto']=$model->getRow('user',$where);

        echo view('header',$kui);
        echo view('menu');
        echo view('pengembalian/pengembalian');
        echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function tambah_pengembalian()
    {
        if (session()->get('level') == 1 || session()->get('level') == 2) {

        $model=new M_model();
        $on='pengembalian.id_inventaris=inventaris.id_inventaris';
        $on2='pengembalian.pembuat_pengembalian=user.id_user';
        $kui['jofinson']=$model->tampilinventaris('pengembalian', 'inventaris', 'user', $on, $on2, 'tanggal_pengembalian_pengembalian');

        $id=session()->get('id');
        $where=array('id_user'=>$id);
        $kui['foto']=$model->getRow('user',$where);

        $kui['j']=$model->tampil('inventaris'); 

        echo view('header',$kui);
        echo view('menu');
        echo view('pengembalian/tambah_pengembalian');
        echo view('footer'); 
        } else {
            return redirect()->to('/home/dashboard');
        }
    }

    public function aksi_tambah_pengembalian()
    {
        $model=new M_model();
        $inventaris=$this->request->getPost('id_inventaris');
        $jumlah_pengembalian=$this->request->getPost('jumlah_pengembalian');
        $pembuat_pengembalian = session()->get('id');
        $data=array(
            'id_inventaris'=> $inventaris,
            'jumlah_pengembalian'=>$jumlah_pengembalian,
            'pembuat_pengembalian' => $pembuat_pengembalian,
        );
        $model->simpan('pengembalian',$data);
        return redirect()->to('/home/pengembalian');
    }

    public function hapus_pengembalian($id)
    {
        if (session()->get('level') == 1 || session()->get('level') == 2) {

        $model=new M_model();
        $where=array('id_pengembalian'=>$id);
        $model->hapus('pengembalian',$where);
        return redirect()->to('/home/pengembalian');
        } else {
            return redirect()->to('/home/dashboard');
        }
    }


    public function laporan_inventaris()
    {
        if(session()->get('level')== 1) {

        $model=new M_model();
        $kui['kunci']='view_inventaris';

        $id=session()->get('id');
        $where=array('id_user'=>$id);
        $kui['foto']=$model->getRow('user',$where);

        echo view('header',$kui);
        echo view('menu',$kui);
        echo view('laporan/filter',$kui);
        echo view('footer');
        }else{
            return redirect()->to('/home/dashboard');
        }
    }
    public function cari_inventaris()
    {
        if(session()->get('level')== 1) {

        $model=new M_model();
        $awal= $this->request->getPost('awal');
        $akhir= $this->request->getPost('akhir');
        $kui['jofinson']=$model->filter_inventaris('inventaris',$awal,$akhir);

        echo view('laporan/c_inventaris',$kui);
        }else{
            return redirect()->to('/home/dashboard');
        }
    }
    public function pdf_inventaris()
    {
        if(session()->get('level')== 1) {

        $model=new M_model();
        $awal= $this->request->getPost('awal');
        $akhir= $this->request->getPost('akhir');
        $kui['jofinson']=$model->filter_inventaris('inventaris',$awal,$akhir);

        $dompdf = new\Dompdf\Dompdf();
        $dompdf->loadHtml(view('laporan/c_inventaris',$kui));
        $dompdf->setPaper('A4','landscape');
        $dompdf->render();
        $dompdf->stream('my.pdf', array('Attachment'=>0));
        }else{
            return redirect()->to('/home/dashboard');
        }
    }
    public function excel_inventaris()
    {
        if(session()->get('level')== 1) {

        $model=new M_model();
        $awal= $this->request->getPost('awal');
        $akhir= $this->request->getPost('akhir');
        $data=$model->filter_inventaris('inventaris',$awal,$akhir);

        $spreadsheet=new Spreadsheet();

        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Nama Inventaris')
        ->setCellValue('B1', 'Kode Inventaris')
        ->setCellValue('C1', 'Kondisi')
        ->setCellValue('D1', 'Keterangan')
        ->setCellValue('E1', 'Jumlah')
        ->setCellValue('F1', 'Nama Jenis')
        ->setCellValue('G1', 'Nama Ruang')
        ->setCellValue('H1', 'Tanggal');

        $total = 0;

        $column=2;

        foreach($data as $data){
            if ($data->keterangan_inventaris == "Dapat Digunakan") {

                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'. $column, $data->nama)
                ->setCellValue('B'. $column, $data->kode_inventaris)
                ->setCellValue('C'. $column, $data->kondisi)
                ->setCellValue('D'. $column, $data->keterangan_inventaris)
                ->setCellValue('E'. $column, $data->jumlah)
                ->setCellValue('F'. $column, $data->nama_jenis)
                ->setCellValue('G'. $column, $data->nama_ruang)
                ->setCellValue('H'. $column, $data->tanggal_tanggal);

                $column++;
            }}
            $writer = new XLsx($spreadsheet);
            $fileName = 'Laporan Inventaris';

            header('Content-type:vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition:attachment;filename='.$fileName.'.xls');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            }else{
                return redirect()->to('/home/dashboard');
            }
        }


        public function laporan_peminjaman()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $kui['kunci']='view_peminjaman';

            $id=session()->get('id');
            $where=array('id_user'=>$id);
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu',$kui);
            echo view('laporan/filter',$kui);
            echo view('footer');
        }else{
            return redirect()->to('/home/dashboard');
        }
        }
        public function cari_peminjaman()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $awal= $this->request->getPost('awal');
            $akhir= $this->request->getPost('akhir');
            $kui['jofinson']=$model->filter_peminjaman('peminjaman',$awal,$akhir);

            echo view('laporan/c_peminjaman',$kui);
        }else{
            return redirect()->to('/home/dashboard');
        }
        }
        public function pdf_peminjaman()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $awal= $this->request->getPost('awal');
            $akhir= $this->request->getPost('akhir');
            $kui['jofinson']=$model->filter_peminjaman('peminjaman',$awal,$akhir);

            $dompdf = new\Dompdf\Dompdf();
            $dompdf->loadHtml(view('laporan/c_peminjaman',$kui));
            $dompdf->setPaper('A4','landscape');
            $dompdf->render();
            $dompdf->stream('my.pdf', array('Attachment'=>0));
        }else{
            return redirect()->to('/home/dashboard');
        }
        }
        public function excel_peminjaman()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $awal= $this->request->getPost('awal');
            $akhir= $this->request->getPost('akhir');
            $data=$model->filter_peminjaman('peminjaman',$awal,$akhir);

            $spreadsheet=new Spreadsheet();

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Username')
            ->setCellValue('B1', 'Nama Inventaris')
            ->setCellValue('C1', 'Kode Inventaris')
            ->setCellValue('D1', 'Jumlah')
            ->setCellValue('E1', 'Tanggal Peminjaman')
            ->setCellValue('F1', 'Tanggal Pengembalian');

            $total = 0;

            $column=2;

            foreach($data as $data){

                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'. $column, $data->username)
                ->setCellValue('B'. $column, $data->nama)
                ->setCellValue('C'. $column, $data->kode_inventaris)
                ->setCellValue('D'. $column, $data->jumlah_pinjam)
                ->setCellValue('E'. $column, $data->tanggal_peminjaman)
                ->setCellValue('F'. $column, $data->tanggal_pengembalian);

                $column++;
            }
            $writer = new XLsx($spreadsheet);
            $fileName = 'Laporan Peminjaman';

            header('Content-type:vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition:attachment;filename='.$fileName.'.xls');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            }else{
                return redirect()->to('/home/dashboard');
            }
        }


        public function laporan_pengembalian()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $kui['kunci']='view_pengembalian';

            $id=session()->get('id');
            $where=array('id_user'=>$id);
            $kui['foto']=$model->getRow('user',$where);

            echo view('header',$kui);
            echo view('menu',$kui);
            echo view('laporan/filter',$kui);
            echo view('footer');
        }else{
            return redirect()->to('/home/dashboard');
        }
        }
        public function cari_pengembalian()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $awal= $this->request->getPost('awal');
            $akhir= $this->request->getPost('akhir');
            $kui['jofinson']=$model->filter_pengembalian('pengembalian',$awal,$akhir);

            echo view('laporan/c_pengembalian',$kui);
        }else{
            return redirect()->to('/home/dashboard');
        }
        }
        public function pdf_pengembalian()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $awal= $this->request->getPost('awal');
            $akhir= $this->request->getPost('akhir');
            $kui['jofinson']=$model->filter_pengembalian('pengembalian',$awal,$akhir);

            $dompdf = new\Dompdf\Dompdf();
            $dompdf->loadHtml(view('laporan/c_pengembalian',$kui));
            $dompdf->setPaper('A4','landscape');
            $dompdf->render();
            $dompdf->stream('my.pdf', array('Attachment'=>0));
        }else{
            return redirect()->to('/home/dashboard');
        }
        }
        public function excel_pengembalian()
        {
        if(session()->get('level')== 1) {

            $model=new M_model();
            $awal= $this->request->getPost('awal');
            $akhir= $this->request->getPost('akhir');
            $data=$model->filter_pengembalian('pengembalian',$awal,$akhir);

            $spreadsheet=new Spreadsheet();

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Username')
            ->setCellValue('B1', 'Nama Inventaris')
            ->setCellValue('C1', 'Kode Inventaris')
            ->setCellValue('D1', 'Jumlah')
            ->setCellValue('E1', 'Tanggal');

            $total = 0;

            $column=2;

            foreach($data as $data){

                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A'. $column, $data->username)
                ->setCellValue('B'. $column, $data->nama)
                ->setCellValue('C'. $column, $data->kode_inventaris)
                ->setCellValue('D'. $column, $data->jumlah_pengembalian)
                ->setCellValue('E'. $column, $data->tanggal_laporan2);

                $column++;
            }
            $writer = new XLsx($spreadsheet);
            $fileName = 'Laporan Pengembalian';

            header('Content-type:vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition:attachment;filename='.$fileName.'.xls');
            header('Cache-Control: max-age=0');

            $writer->save('php://output');
            }else{
                return redirect()->to('/home/dashboard');
            }
        }
    }
