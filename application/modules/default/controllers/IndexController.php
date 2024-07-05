<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        
    }
    public function indexAction()
    {
      $request = $this->getRequest();
      $kode_biller = '059';
      $biller     = $request->getParam('transferDana');


        if($request->getPost('proses') == 'importDoc') {
            $dirfile    = 'media/';
            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination(
               $dirfile
            )
               ->addValidator(
                  'Extension',
                  false,
                  'txt,TXT,csv,CSV'
               )->addValidator(
                  'Size',
                  false,
                  array(
                     'min' => '1b',
                     'max' => '5MB',
                     'bytestring' => false
                  )
               );
               if (!$upload->isValid()) {
                $this->view->resftr = Default_Model_Aps::STATUS('02', 'Ekstensi file support txt / csv, max file size 5MB');
             } else {
                try {
                   $upload->receive();
 
                   chmod(
                      $dirfile . $_FILES['fileftrkis']['name'],
                      0777
                   );

                  Default_Model_Aps::ImportFTR($dirfile . $_FILES['ftrFile']['name']);
                  $keterangan = "UPLOAD FTR : " . $_FILES['ftrFile']['name'] . " BERHASIL";
                  $this->view->resftr = Default_Model_Aps::SUKSESTAG($keterangan);
             } catch (Exception $e) {
               echo $e;
             }
         }
      } elseif ($request->getPost('proses') == 'generateDoc') {
         $keterangan = "GENERATE TRANSFER DANA BERHASIL " . $biller;
       
         foreach (Default_Model_Aps::VIEWALLTGLTRXREKON('059') as $tgl) {
            $tglawalx[]        = $tgl['tgl1']; //tgl_trx
            $tglawalakhirx[]   = $tgl['tgl2']; //tgl_trx
            $tglprosesx[]      = $tgl['tglrekon'];
         }

         if ($biller == 'MANDIRI_MT940') {
            try {
               Default_Model_Aps::GenerateTransferDana('MANDIRI', $tglprosesx[0]);

               $this->view->resTransferDana = Default_Model_Aps::SUKSESTAG($keterangan);
            } catch (Exception $e) {
               $message = $e->getMessage();
               $code = $e->getCode();
               $keterangan = "GAGAL MELAKUKAN GENERATE TRANSFER DANA";

               $this->view->resftr = Default_Model_Aps::ERRORTAG($keterangan . ":" . $message . $code);
            }
         } elseif ($biller == 'INDOMARET') {
            try {
               Default_Model_Aps::GenerateTransferDana('INDOMARET', $tglprosesx[0]);

               /*** LOG REKON ***/
               Default_Model_Aps::logrekon('FTR TRANSFER DANA ', $_SESSION['usernameitops'], $keterangan, 'Selesai');
               $this->view->resftr = Default_Model_Aps::SUKSESTAG($keterangan);
               
            } catch (Exception $e) {
               $message = $e->getMessage();
               $code = $e->getCode();
               $keterangan = "GAGAL MELAKUKAN GENERATE TRANSFER DANA";

               $this->view->resftr = Default_Model_Aps::ERRORTAG($keterangan . ":" . $message . $code);
            }
         } else {
            $this->view->resTransferDana = "Pilih Menu Indomaret /  Mandiri_MT940 Terlebih Dahulu";
         }
      } elseif ($request->getPost('proses') == 'importLocal'){

         $dirfile    = 'emoneys/';

            $upload = new Zend_File_Transfer_Adapter_Http();
            $upload->setDestination(
               $dirfile
            )
               ->addValidator(
                  'Extension',
                  false,
                  'txt,TXT,csv,CSV'
               )->addValidator(
                  'Size',
                  false,
                  array(
                     'min' => '1b',
                     'max' => '5MB',
                     'bytestring' => false
                  )
               );

            if (!$upload->isValid()) {
               $this->view->resftr = Multibiller_Model_Aps::STATUS('02', 'Ekstensi file support txt / csv, max file size 5MB');
            } else {
               $upload->receive();

               chmod($dirfile . $_FILES['fileftrkis']['name'], 0777);
               $nm_file           = $_FILES['fileftrkis']['name'];
               $nm_file_output    = "MT940_TRANSFER_DANA_" . $nm_file;

               $status = Indomaret_Model_Aps::konvertMt940TfDanaIndomaret($nm_file, $dirfile . $nm_file, $dirfile . $nm_file_output, $kode_biller);
               $status = Default_Model_Aps::konvertMt940TfDanaIndomaret($nm_file, $dirfile . $nm_file, $dirfile . $nm_file_output, $kode_biller);

               $this->view->resftr = Pln_Model_Aps::SUKSESTAG("INSERT TABLE FTR SELESAI " . $nm_file_output);

               // if ($status == "done") {
               //    chmod($dirfile . $nm_file_output, 0777);


               //    //     //INSERT KE TABLE
               //    $ftp_conn      = ssh2_connect($this->ftp_server, 22) or die("Could not connect to SFTP " . $this->ftp_server);
               //    $login         = ssh2_auth_password($ftp_conn, $this->usernameftp, $this->passwordftp);
               //    $sftp          = ssh2_sftp($ftp_conn);
               //    $remote_dir    = "/usr/production/REKON/proses/tampungan_file_webrekon/";


               //    if (ssh2_scp_send($ftp_conn, $dirfile . $nm_file_output, $remote_dir . $nm_file_output, 0644)) {
               //       try {
               //          // Multibiller_Model_Aps::TRUNCATEX('FTR_VA_MT940');

                        Indomaret_Model_Aps::loadFtrIndomaret($remote_dir . $nm_file_output);

               //          $rc = "00";
               //       } catch (Exception $e) {
               //          /*** LOG REKON ***/
               //          Pln_Model_Aps::logrekon(
               //             'TRANSFER DANA',
               //             $_SESSION['usernameitops'],
               //             "INSERT TABLE FTR " . $nm_file_output,
               //             'GAGAL'
               //          );

               //          $this->view->resftr = Pln_Model_Aps::ERRORTAG("INSERT TABLE FTR GAGAL, HUBUNGI ADMIN : " . $nm_file_output . $e->getCode() . $e->getMessage());
               //       }

               //       if ($rc == "00") {
               //          /*** LOG REKON ***/
               //          Pln_Model_Aps::logrekon(
               //             'TRANSFER DANA',
               //             $_SESSION['usernameitops'],
               //             "INSERT TABLE FTR " . $nm_file_output,
               //             'SUKSES'
               //          );

               //          $this->view->resftr = Pln_Model_Aps::SUKSESTAG("INSERT TABLE FTR SELESAI " . $nm_file_output);
               //       }
               //    } else {
               //       /*** LOG REKON kirim ftp***/
               //       Pln_Model_Aps::logrekon('TRANSFER DANA', $_SESSION['usernameitops'], $nm_file_output, 'GAGAL KIRIM FTP TAMPUNGAN');

               //       $this->view->resftr = Pln_Model_Aps::ERRORTAG($nm_file_output . " GAGAL KIRIM FTP TAMPUNGAN, HUBUNGI ADMIN : ");
               //    }
               // }
            }
         }
      }
}