<?php
class parcall extends CI_Controller{
     public function create_label()
      {
          $order_post = $this->order_request();
          $order_data = json_decode($order_post);
        if($order_data->Message == 'Submission succeeded!'){
            echo "Sipariş Başarıyla Alındı!";
            echo "<br><br>";
            echo "Sipariş Bilgileri =>";
            echo "<br>";
            echo "Sipariş tarihi: ".$order_data->TimeStamp;
            echo "<br>";
            echo "Sipariş Takip Numarası: ".$order_data->Item[0]->TrackingNumber;
            echo "<br><br>";
            $print_post = $this->label_print();
            $print_data = json_decode($print_post);
            //var_dump($print_data->Item[0]->Url);
            if($print_data->Message == 'Submission succeeded!'){
                echo "Etiket Başarıyla Alındı!";
                echo "<br><br>";
                echo "Etiket Bilgileri =>";
                echo "<br>";
                echo "Etiket URL: " .$print_data->Item[0]->Url;
                echo "<br>";
                echo "Etiket Türü: ".mb_strtoupper($print_data->Item[0]->LabelType);
                echo "<br>";
                echo "Etiket Dizesi: ".$print_data->Item[0]->LabelString;
                echo "<br><br>";
                $tracking_data = $this->tracking_info();
                $tracking_post = json_decode($tracking_data);
                if($tracking_post->Message == 'Submission succeeded!'){
                    echo "Nakliye Bilgiler Alındı!";
                    echo "<br><br>";
                    echo "Nakliye Bilgileri =>";
                    echo "<br>";
                    echo "İşlem İçeriği: ".$tracking_post->Item->OrderTrackingDetails[0]->ProcessContent;
                    echo "<br>";
                    echo "İşlem Konumu: ".$tracking_post->Item->OrderTrackingDetails[0]->ProcessLocation;
                    echo "<br>";
                    echo "İşlem Tarihi: ".$tracking_post->Item->OrderTrackingDetails[0]->ProcessDate;
                    echo "<br>";
                    echo "İzleme Durumu: ".$tracking_post->Item->OrderTrackingDetails[0]->TrackingStatus;
                    echo "<br><br>";
                    exit();
                }
                else{
                    echo $tracking_post->Message;
                    echo "<br><br>";
                }
            }else{
                echo $print_data->Message;
            }
            exit();
        }else{
            echo $order_data->Message;
            echo "<br>";
            exit();
        }
      }

    public function order_request(){
        $token = "Basic ".base64_encode(PARCEL_API_USER."&".PARCEL_API_KEY);
        $post_data = array();
        $post_data["CustomerOrderNumber"]="C12345679822";
        $post_data["ShippingMethodCode"]="ECOWE";
        $post_data["PackageCount"]=1;
        $post_data["Weight"]=0.1;
        $post_data["WeightUnits"]="lbs";
        $post_data["LabelType"]="ZPL";
        $receiver= array("CountryCode"=>"US","FirstName"=>"Patrick","Lastname"=>"Mills","Company"=>"CompanyName",
            "Street"=>"545 San Pedro St","City"=>"LosAngeles","State"=>"CA","Zip"=>"90013","Phone"=>"+5869098233",
            "Email"=>"asdasdsadsd@gmail.com");
        $post_data["Receiver"]= $receiver;
        $sender = array("CountryCode"=>"US","FirstName"=>"Peter","Lastname"=>"Miller","Company"=>"Senderltd.",
            "Street"=>"2017 TELLURIDE DR","City"=>"GEORGETOWN","State"=>"TX","Zip"=>"78626","Phone"=>"+5869098233",
            "Email"=>"asdasdsadsd@gmail.com");
        $post_data["Sender"] = $sender;
        $parcels = array(array("EName"=>"cotton shirt for men","HSCode"=>"HSCode","Quantity"=>1,"SKU"=>"sku1001.","UnitPrice"=>10,
            "UnitWeight"=>0.5,"CurrencyCode"=>"USD"));
        $post_data["Parcels"] = $parcels;
        $post_data =  array($post_data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://gapi.yunexpressusa.com/api/WayBill/CreateOrder',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_POSTFIELDS =>json_encode($post_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: '.$token
            ),
        ));

          return $response = curl_exec($curl);
        curl_close($curl);
     // var_dump(json_decode($response));


    }

    public function label_print(){
        $token = "Basic ".base64_encode(PARCEL_API_USER."&".PARCEL_API_KEY);
        $post_data = array();
        $post_data[]="C12345679822";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>'https://gapi.yunexpressusa.com/api/Label/Print',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_POSTFIELDS =>json_encode($post_data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: '.$token
            ),
        ));
        return  $response = curl_exec($curl);
        curl_close($curl);
        //var_dump($response);
    }

    public function tracking_info()
    {
        $token = "Basic ".base64_encode(PARCEL_API_USER."&".PARCEL_API_KEY);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://gapi.yunexpressusa.com/api/Tracking/GetTrackInfo?OrderNumber=C12345679822',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER=>false,
            CURLOPT_SSL_VERIFYHOST=>false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: '.$token
            ),
        ));

         return $response = curl_exec($curl);
        curl_close($curl);
        //var_dump(json_decode($response));
    }

    public function query_rates($country_code,$weight,$weightunits,$package_type,$post_code,$origin)
    {
        $token = "Basic " . base64_encode(PARCEL_API_USER . "&" . PARCEL_API_KEY);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>'https://gapi.yunexpressusa.com/api/Freight/GetPriceTrial?CountryCode=' . $country_code . '&Weight=' . $weight . '&weightunits=' . $weightunits . '&PackageType=' . $package_type . '&PostCode=' . $post_code . '&Origin=' . $origin . '',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . $token
            ),
        ));
        return $response = curl_exec($curl);
        curl_close($curl);
        $queryRatesData = json_decode($response);
            //var_dump($queryRatesData->Items[0]->CName);
            if ($queryRatesData->Message == 'Submission succeeded!') {
                echo "Fiyat Bilgileri Başarıyla Alındı";
                echo "<br><br>";
                echo "Fiyat Bilgileri: ";
                echo "<br>";
                echo "Fiyat Kodu: " . $queryRatesData->Items[0]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[0]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[0]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[0]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[0]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[0]->product_groupname;
                echo "<br><br>";

                echo "Fiyat Kodu: " . $queryRatesData->Items[1]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[1]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[1]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[1]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[1]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[1]->product_groupname;
                echo "<br><br>";

                echo "Fiyat Kodu: " . $queryRatesData->Items[2]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[2]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[2]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[2]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[2]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[2]->product_groupname;
                echo "<br><br>";

                echo "Fiyat Kodu: " . $queryRatesData->Items[3]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[3]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[3]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[3]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[3]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[3]->product_groupname;
                echo "<br><br>";

                echo "Fiyat Kodu: " . $queryRatesData->Items[4]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[4]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[4]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[4]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[4]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[4]->product_groupname;
                echo "<br><br>";

                echo "Fiyat Kodu: " . $queryRatesData->Items[5]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[5]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[5]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[5]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[5]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[5]->product_groupname;
                echo "<br><br>";

                echo "Fiyat Kodu: " . $queryRatesData->Items[6]->Code;
                echo "<br>";
                echo "Ename: " . $queryRatesData->Items[6]->EName;
                echo "<br>";
                echo "Cname: " . $queryRatesData->Items[6]->CName;
                echo "<br>";
                echo "Nakliye Ücreti: " . $queryRatesData->Items[6]->ShippingFee;
                echo "<br>";
                echo "Toplam Ücret: " . $queryRatesData->Items[6]->TotalFee;
                echo "<br>";
                echo "Ürün Grup Adı: " . $queryRatesData->Items[6]->product_groupname;
                echo "<br><br>";
            } else {
                echo $queryRatesData->Message;
            }
    }
}
?>
