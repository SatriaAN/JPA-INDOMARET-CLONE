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
            )->addValidator(
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
                      $dirfile . $_FILES['ftrFile']['name'],
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

         $this->view->dataIndomaret = Default_Model_Aps::GENERATEDOC();

         // if ($biller == 'MANDIRI_MT940') {
         //    try {
         //       Default_Model_Aps::GenerateTransferDana('MANDIRI', $tglprosesx[0]);

         //       $this->view->resTransferDana = Default_Model_Aps::SUKSESTAG($keterangan);
         //    } catch (Exception $e) {
         //       $message = $e->getMessage();
         //       $code = $e->getCode();
         //       $keterangan = "GAGAL MELAKUKAN GENERATE TRANSFER DANA";

         //       $this->view->resftr = Default_Model_Aps::ERRORTAG($keterangan . ":" . $message . $code);
         //    }
         // } elseif ($biller == 'INDOMARET') {
         //    try {
         //       Default_Model_Aps::GenerateTransferDana('INDOMARET', $tglprosesx[0]);

         //       /*** LOG REKON ***/
         //       Default_Model_Aps::logrekon('FTR TRANSFER DANA ', $_SESSION['usernameitops'], $keterangan, 'Selesai');
         //       $this->view->resftr = Default_Model_Aps::SUKSESTAG($keterangan);
               
         //    } catch (Exception $e) {
         //       $message = $e->getMessage();
         //       $code = $e->getCode();
         //       $keterangan = "GAGAL MELAKUKAN GENERATE TRANSFER DANA";

         //       $this->view->resftr = Default_Model_Aps::ERRORTAG($keterangan . ":" . $message . $code);
         //    }
         // } else {
         //    $this->view->resTransferDana = "Pilih Menu Indomaret /  Mandiri_MT940 Terlebih Dahulu";
         // }
      } elseif ($request->getPost('proses') == 'delete') {
         $idsToDelete = $request->getPost('delete');
         if (!empty($idsToDelete)) {
             foreach ($idsToDelete as $id) {
                 Default_Model_Aps::DELETE_RECORD($id);
             }
             $this->_redirect('/index');
         }
      }
    }
}