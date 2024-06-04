<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class Mahasiswa extends CI_Controller
{
    public function index()
    {
        $data['mahasiswa'] = $this->m_mahasiswa->tampil_data()->result();
        $this->load->view('template/header');
        $this->load->view('template/sidebar');
        $this->load->view('mahasiswa', $data);
        $this->load->view('template/footer');
    }

    public function input_data($data)
    {
        $this->db->insert('tb_mahasiswa', $data);
    }
    public function tambah()
    {
        $this->load->view('template/header');
        $this->load->view('template/sidebar');
        $this->load->view('mahasiswa');
        $this->load->view('template/footer');
    }

    public function tambah_aksi()
    {
        $nama       = $this->input->post('nama');
        $nim        = $this->input->post('nim');
        $tgl_lahir  = $this->input->post('tgl_lahir');
        $jurusan    = $this->input->post('jurusan');
        $alamat     = $this->input->post('alamat');
        $email      = $this->input->post('email');
        $no_telp    = $this->input->post('no_telp');
        $foto       = $_FILES['foto'];

        if ($foto == '') {
        } else {
            $config['upload_path']      = './assets/foto';
            $config['allowed_types']      = 'jpg|png|gif';

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('foto')) {
                echo "Upload Gagal";
                die();
            } else {
                $foto = $this->upload->data('file_name');
            }
        }



        $data = array(
            'nama'        => $nama,
            'nim'         => $nim,
            'tgl_lahir'   => $tgl_lahir,
            'jurusan'     => $jurusan,
            'alamat'      => $alamat,
            'email'       => $email,
            'no_telp'     => $no_telp,
            'foto'        => $foto,


        );

        $this->m_mahasiswa->input_data($data, 'tb_mahasiswa');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
  Data Berhasil di tambahkan
</div>');
        redirect('mahasiswa/index');
    }

    public function hapus($id)
    {
        $where = array('id' => $id);

        $this->m_mahasiswa->hapus_data($where, 'tb_mahasiswa');
        $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
  Data Berhasil di hapus
</div>');
        redirect('mahasiswa/index');
    }

    public function edit($id)
    {
        $where = array('id' => $id);
        $data['mahasiswa'] = $this->m_mahasiswa->edit_data($where, 'tb_mahasiswa')->result();
        $this->load->view('template/header');
        $this->load->view('template/sidebar');
        $this->load->view('edit', $data);
        $this->load->view('template/footer');
    }

    public function update()
    {
        $id = $this->input->post('id');
        $nama = $this->input->post('nama');
        $nim = $this->input->post('nim');
        $tgl_lahir = $this->input->post('tgl_lahir');
        $jurusan = $this->input->post('jurusan');
        $alamat = $this->input->post('alamat');
        $email = $this->input->post('email');
        $no_telp = $this->input->post('no_telp');

        $data = array(
            'nama'      => $nama,
            'nim'       => $nim,
            'tgl_lahir' => $tgl_lahir,
            'jurusan'   => $jurusan,
            'alamat'      => $alamat,
            'email'       => $email,
            'no_telp'     => $no_telp,

        );
        $where = array(
            'id' => $id
        );

        $this->m_mahasiswa->update_data($where, $data, 'tb_mahasiswa');
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
 Data berhasil di update
</div>');
        redirect('mahasiswa/index');
    }
    public function detail($id)
    {
        $this->load->model('m_mahasiswa');
        $detail = $this->m_mahasiswa->detail_data($id);
        $data['detail'] = $detail;
        $this->load->view('template/header');
        $this->load->view('template/sidebar');
        $this->load->view('detail', $data);
        $this->load->view('template/footer');
    }

    public function print()
    {
        $data['mahasiswa'] = $this->m_mahasiswa->tampil_data("tb_mahasiswa")->result();
        $this->load->view('print_mahasiswa', $data);
    }

    public function generatePDF()
    {
        $data['mahasiswa'] = $this->m_mahasiswa->tampil_data("tb_mahasiswa")->result();

        $this->load->library('pdf');

        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->filename = "laporan_mahasiswa.pdf";
        $this->pdf->load_view('laporan_pdf', $data);
    }



    public function excel()
    {
        $this->load->model('m_mahasiswa');
        $mahasiswa_data = $this->m_mahasiswa->tampil_data('tb_mahasiswa')->result();

        // Load PhpSpreadsheet namespace

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        $spreadsheet->getProperties()->setCreator('Your Name')
            ->setTitle('Data Mahasiswa')
            ->setSubject('Data Mahasiswa')
            ->setDescription('Data Mahasiswa')
            ->setKeywords('Data Mahasiswa');

        $spreadsheet->setActiveSheetIndex(0);

        // Add headers
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'NO');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'NAMA MAHASISWA');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'NIM');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'TANGGAL LAHIR');
        $spreadsheet->getActiveSheet()->setCellValue('E1', 'JURUSAN');
        $spreadsheet->getActiveSheet()->setCellValue('F1', 'ALAMAT');
        $spreadsheet->getActiveSheet()->setCellValue('G1', 'EMAIL');
        $spreadsheet->getActiveSheet()->setCellValue('H1', 'NO TELPON');

        // Add data
        $baris = 2;
        $no = 1;
        foreach ($mahasiswa_data as $item) {
            $spreadsheet->getActiveSheet()->setCellValue('A' . $baris, $no++);
            $spreadsheet->getActiveSheet()->setCellValue('B' . $baris, $item->nama);
            $spreadsheet->getActiveSheet()->setCellValue('C' . $baris, $item->nim);
            $spreadsheet->getActiveSheet()->setCellValue('D' . $baris, $item->tgl_lahir);
            $spreadsheet->getActiveSheet()->setCellValue('E' . $baris, $item->jurusan);
            $spreadsheet->getActiveSheet()->setCellValue('F' . $baris, $item->alamat);
            $spreadsheet->getActiveSheet()->setCellValue('G' . $baris, $item->email);
            $spreadsheet->getActiveSheet()->setCellValue('H' . $baris, $item->no_telp);
            $baris++;
        }

        $filename = "DATA_MAHASISWA.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    public function search()
    {
        $keyword = $this->input->post('keyword');
        $data['mahasiswa'] = $this->m_mahasiswa->get_keyword($keyword);
        $this->load->view('template/header');
        $this->load->view('template/sidebar');
        $this->load->view('mahasiswa', $data);
        $this->load->view('template/footer');
    }
}
