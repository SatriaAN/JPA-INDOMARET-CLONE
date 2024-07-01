<?php

class Default_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        
    }
    public function indexAction()
    {
        $request = $this->getRequest();

        if($request->getPost('proses') == 'importftDoc') {
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
                   /*execute*/
                   $res = Default_Model_Aps::ImportFTR($dirfile . $_FILES['ftrFile']['name']);
 
                //    if ($res !== null) {
                //       /*** LOG REKON ***/
                //       $keterangan = "UPLOAD FTR TRANSFER DANA INDOMARET : " . $_FILES['ftrFile']['name'] . " GAGAL -" . $res;
                //       Pln_Model_Aps::logrekon('TRANSFER DANA', $_SESSION['usernameitops'], $keterangan, 'GAGAL');
 
                //       $this->view->resftr = Pln_Model_Aps::ERRORTAG($keterangan);
                //    } else {
                //       /*** LOG REKON ***/
                //       $keterangan = "UPLOAD FTR TRANSFER DANA INDOMARET : " . $_FILES['ftrFile']['name'];
                //       Pln_Model_Aps::logrekon('TRANSFER DANA INDOMARET', $_SESSION['usernameitops'], $keterangan, 'Selesai');
 
                //       $this->view->resftr = Pln_Model_Aps::SUKSESTAG($keterangan);
                //       // Multibiller_Model_Aps::REFRESHPAGE();
                //    }
                } catch (Exception $e) {
                   $keterangan = "UPLOAD FTR TRANSFER DANA INDOMARET : " . $_FILES['ftrFile']['name'] . " GAGAL -" . $res;
 
                   $message    = $e->getMessage();
                   $code       = $e->getCode();
                   $res        = '02';
 
                   $this->view->resftr = Default_Model_Aps::ERRORTAG($message . ' ' . $code);
                }
                /*show notif*/
                if ($res == '02') {
                   /*** LOG REKON ***/
                   $keterangan = "UPLOAD FTR  : " . $_FILES['ftrFile']['name'] . " GAGAL";
                   Default_Model_Aps::logrekon('', $_SESSION['usernameitops'], $keterangan, 'GAGAL');
                }
                /* unlink tampungan local */
                // array_map('unlink', glob($dirfile.$_FILES['fileftrpegadaianidm']['name']));
             }
        } elseif ($request->getPost('proses') == 'generateDoc') {

            $keterangan = "GENERATE TRANSFER DANA BERHASIL " . $biller;
   
            if ($biller == 'MT940_mandiri') {
               try {
                  Indomaret_Model_Aps::GenerateTransferDana('MDR', $tglprosesx[0]);
   
                  $this->view->resftr = Pln_Model_Aps::SUKSESTAG($keterangan);
               } catch (Exception $e) {
                  $message = $e->getMessage();
                  $code = $e->getCode();
                  $keterangan = "GAGAL MELAKUKAN GENERATE TRANSFER DANA";
   
                  $this->view->resftr = Pln_Model_Aps::ERRORTAG($keterangan . ":" . $message . $code);
               }
            } else {
               try {
                  Indomaret_Model_Aps::GenerateTransferDana('KIS', $tglprosesx[0]);
   
                  /*** LOG REKON ***/
                  Pln_Model_Aps::logrekon('FTR TRANSFER DANA ', $_SESSION['usernameitops'], $keterangan, 'Selesai');
                  $this->view->resftr = Pln_Model_Aps::SUKSESTAG($keterangan);
               } catch (Exception $e) {
                  $message = $e->getMessage();
                  $code = $e->getCode();
                  $keterangan = "GAGAL MELAKUKAN GENERATE TRANSFER DANA";
   
                  $this->view->resftr = Pln_Model_Aps::ERRORTAG($keterangan . ":" . $message . $code);
               }
            }
        }
    }
}