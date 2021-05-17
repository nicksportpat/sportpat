<?php
namespace Sportpat\Sync\Helper;
use MongoDB\Client;

class Images extends \Magento\Framework\App\Helper\AbstractHelper {

    // class for image assignment



    private $_imagedir;
    private $_imagedir_matrixes;

    private $_jsonarray;
    private $_mongo;
    private $fp;

    public function __construct() {

        $this->_imagedir = "/data/home/sportpat/pub/media/import/items/";
        $this->_imagedir_matrixes = "/data/home/sportpat/pub/media/import/matrix/";

    }

    public function getJSONFile($lsid="") {

        if($lsid === "") {
            return false;
        }

        if(file_exists($this->_imagedir.$lsid.".json")) {

            $j = file_get_contents($this->_imagedir . $lsid . ".json");
             $this->_jsonarray = json_decode($j, true);
            return true;
        } else {
            return false;
        }

    }

    public function getJSONFileMatrix($lsid="") {

        if($lsid === "") {
            return false;
        }

        if(file_exists($this->_imagedir_matrixes.$lsid.".json")) {

            $j = file_get_contents($this->_imagedir_matrixes . $lsid . ".json");
            $this->_jsonarray = json_decode($j, true);

            return true;
        } else {
            return false;
        }

    }

    public function getNewImages($lsid) {


//echo $lsid;
   /*   if($this->getJSONFile($lsid) !== false) {

          $ni = [];
          $i = 0;
          $n = 0;

          if (isset($this->_jsonarray["newName"])) {
              $images = $this->_jsonarray["newName"];

              foreach ($images as $image) {

                  $imm = str_replace("/data/home/sportpat/pub/media/", "/data/home/sportpat/pub/media/", $image);
                  if (file_exists($imm)) {

                      $exp = explode("__", $imm);
                      $position = $exp[0];
                      $position = explode("/optimized/", $position);


                      $ni[$i]['image'] = $imm;
                      $ni[$i]['position'] = $position[1];

                  }





                  $i++;
              }


              return $ni;
          }
      } else {*/
          $this->_mongo = (new Client("mongodb://127.0.0.1:27017"))->sportpat_batchmay;
          $doc = $this->_mongo->items->find(["lightspeed_id"=> $lsid]);
          if($doc) {
              $ni = [];
              $i=0;
              foreach($doc as $item) {
                  $images = $item["images"];
                  foreach ($images as $image) {
                      $baseURL = $image["baseImageURL"];
                      $options = "w_800/";
                      $ext = ".jpg";
                      $pubID = $image["publicID"];
                      $url = $baseURL . $options . $pubID . $ext;

                      $this->fp = fopen("/data/home/sportpat/pub/media/images/items/" . $lsid . "-" . $pubID . $ext, 'w+');
                      $im = $this->grab_image($url);
                      fclose($this->fp);
                      $ni[$i]['image'] = "/data/home/sportpat/pub/media/images/items/" . $lsid . "-" . $pubID . $ext;
                      $ni[$i]['position'] = $image['ordering'];

                      $i++;

                  }
              }

             return $ni;

          }
      //}
        return null;
    }

    public function getNewImagesForMatrix($lsid) {



       /* if($this->getJSONFileMatrix($lsid) !== false) {

            $ni = [];
            $i = 0;
            $n = 0;

            if (isset($this->_jsonarray["newName"])) {
                $images = $this->_jsonarray["newName"];

                foreach ($images as $image) {

                    $imm = str_replace("/home/tmpimages/", "/data/home/sportpat/pub/media/import/matrix/", $image);
                    if (file_exists($imm)) {

                        $exp = explode("__", $imm);
                        $position = $exp[0];
                        $position = explode("/optimized/", $position);


                        $ni[$i]['image'] = $imm;
                        $ni[$i]['position'] = $position[1];

                    }





                    $i++;
                }


                return $ni;
            }
        }*/

        $this->_mongo = (new Client("mongodb://127.0.0.1:27017"))->sportpat_batchmay;
        $doc = $this->_mongo->matrixes->find(["lightspeed_id"=> $lsid]);
        if($doc) {
            $ni = [];
            $i=0;
            foreach($doc as $item) {
                $images = $item["images"];
                foreach ($images as $image) {
                    $baseURL = $image["baseImageURL"];
                    $options = "w_800/";
                    $ext = ".jpg";
                    $pubID = $image["publicID"];
                    $url = $baseURL . $options . $pubID . $ext;

                    $this->fp = fopen("/data/home/sportpat/pub/media/images/items/" . $lsid . "-" . $pubID . $ext, 'w+');
                    $im = $this->grab_image($url);
                    fclose($this->fp);
                    $ni[$i]['image'] = "/data/home/sportpat/pub/media/images/items/" . $lsid . "-" . $pubID . $ext;
                    $ni[$i]['position'] = $image['ordering'];

                    $i++;

                }
            }

            return $ni;

        }
        return null;
    }


    public function grab_image($image_url){
        $ch = curl_init($image_url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        curl_setopt($ch, CURLOPT_WRITEFUNCTION, array($this, 'curl_callback'));
         curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
        curl_exec($ch);

        curl_close($ch);                              // closing curl handle
    }

    public function curl_callback($ch, $bytes){

        $len = fwrite($this->fp, $bytes);
        // if you want, you can use any progress printing here
        return $len;
    }

}
