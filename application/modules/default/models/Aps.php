<?php
	class Default_Model_Aps
	{
		
		 public static function InitDbRekon()
		 {
			$options = array(
			   Zend_Db::ALLOW_SERIALIZATION => false
			);
	  
			$params = array(
			   // 'host'           => '10.54.54.14',
			   'host'           => '10.254.254.110',
			   'username'       => 'jpa',
			   'password'       => 'jpa123',
			   'dbname'         => 'db_rekon_pln',
			   'options'        => $options
			);
	  
			$db = Zend_Db::factory('Pdo_Mysql', $params);
			return $db;
		 }
		

		 public static function ImportFTR($ftrindomaret)
		 {
			$fp = fopen($ftrindomaret, "r");
			$line = file($ftrindomaret);
			$isifile = fread($fp, filesize($ftrindomaret));
			$params = explode("\n", $isifile);
			$totline = count($line);
			$counter = 0;
	  
			foreach ($params as $isifilex) {
			   if ($counter++ == 0) continue; //skip row
			   // if ($isifilex === NULL) continue; //skip row
			   if (empty($isifilex)) continue; //skip row
			   $exp = explode("|", $isifilex);
	  
			   $vTGL_TRANSAKSI        = $exp[0];
			   $vTERMINAL_ID          = $exp[1];
			   $vNAMA_PP              = $exp[2];
			   $vID_TRX               = $exp[3];
			   $vREFF_ID              = $exp[4];
			   $vTRACE_NUMBER         = $exp[5];
			   $vTRAN_AMOUNT          = $exp[6];
			   $vADMIN_BANK           = $exp[7];
			   $vTRX                  = $exp[8];
			   $vTRANSACTION_NUMBER   = $exp[9];
				
			   try {
				  self::UploadFTR(
					 $vTGL_TRANSAKSI,
					 $vTERMINAL_ID,
					 $vNAMA_PP,
					 $vID_TRX,
					 $vREFF_ID,
					 $vTRACE_NUMBER,
					 $vTRAN_AMOUNT,
					 $vADMIN_BANK,
					 $vTRX,
					 $vTRANSACTION_NUMBER
				  );
				  $res = null; //set respon null jika setiap looping insert sukses
			   } catch (Exception $e) {
				  $res  = $e->getMessage(); //set respon message error jika ada looping yang gagal
			   }
			}
			return $res;
		 }

		 public static function UploadFTR(
			$vTGL_TRANSAKSI,
			$vTERMINAL_ID,
			$vNAMA_PP,
			$vID_TRX,
			$vREFF_ID,
			$vTRACE_NUMBER,
			$vTRAN_AMOUNT,
			$vADMIN_BANK,
			$vTRX,
			$vTRANSACTION_NUMBER
		 ) {
			$db = self::InitDbRekon();
	  
			$sql = "INSERT INTO db_dummy.clone_template_ftr_idm 
			   (
				vTGL_TRANSAKSI
				, vTERMINAL_ID, vNAMA_PP, vID_TRX, vREFF_ID, vTRACE_NUMBER, vTRAN_AMOUNT, vADMIN_BANK, vTRX,
			   vTRANSACTION_NUMBER)
			   VALUES
			   ('" . $vTGL_TRANSAKSI . "','" . $vTERMINAL_ID . "','" . $vNAMA_PP . "','" . $vID_TRX . "','" . $vREFF_ID . "','" . $vTRACE_NUMBER . "','" . $vTRAN_AMOUNT . "','" . $vADMIN_BANK . "',
			   '" . $vTRX . "','" . $vTRANSACTION_NUMBER . "')";
	  
			return $db->fetchAll($sql);
		 }

		 public static function STATUS($code, $alert)
    {
        if ($code == '01') {
            $html = "<div class='alert alert-success alert-dismissable'>
   							<button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button>
   							<h4>
   							<i class='icon fa fa-check'></i>
   							Alert!
   							</h4>
   							" . $alert . "
   						</div>";
        } elseif ($code == '02') {
            $html = "<div class='alert alert-danger alert-dismissable'>
   							<button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button>
   							<h4>
   							<i class='icon fas fa-exclamation-triangle'></i>
   							Alert!
   							</h4>
   							" . $alert . "
   						</div>";
        } elseif ($code == '008') {
            $html = "<div class='alert alert-warning alert-dismissable'>
   							<button class='close' aria-hidden='true' data-dismiss='alert' type='button'>×</button>
   							<h4>
   							<i class='icon fas fa-exclamation-triangle'></i>
   							Alert!
   							</h4>
   							" . $alert . "
   						</div>";
        }
        return $html;
    }

	public static function SUKSESTAG($x)
        {
            $x = "<div class='alert alert-success'>
                    <button class='close' data-dismiss='alert'>×</button>
                    <strong>Success!</strong>
                    ".$x."
                  </div>";

            return $x;
        }

        public static function ERRORTAG($x)
        {
            $x = "<div class='alert alert-error'>
                    <button class='close' data-dismiss='alert'>×</button>
                        <strong>Error!</strong>
                    ".$x."
                  </div>";

            return $x;
        }

        public static function INFOTAG($x)
        {
            $x = "<div class='alert alert-info'>
                    <button class='close' data-dismiss='alert'>×</button>
                    <strong>Info!</strong>
                    ".$x."
                  </div>";

            return $x;
        }

		// public static function logrekon($kdlynftr,$user,$keterangan,$message)
        // {
        //     $db = Default_Model_Aps::InitDbRekon();
        //     $sql = "call db_rekon_pln.SP_kegiatanrekon('".$kdlynftr."', '".$user."', '".$keterangan."', '".$message."')";

        //     $db->query($sql);
        // }

		public static function VIEWALLTGLTRXREKON($kodebiller)
		{
			$db = self::InitDbRekon();
			$sql = "SELECT MIN(TGL_TRANSAKSI) tgl1, MAX(TGL_TRANSAKSI) tgl2, MAX(TGL_PROSES_REKON) tglrekon FROM db_rekon_multi.log_proses_rekon where BILLER = '" . $kodebiller . "'";

			return $db->fetchAll($sql);
		}

	}