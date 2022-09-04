<?php
	define('CI_SESSION', 'cl7udjvkq86pj245l5jud5duahdfi9n6');

    include('simple_html_dom.php');

	function filterData(&$str){ 
	    $str = preg_replace("/\t/", "\\t", $str); 
	    $str = preg_replace("/\r?\n/", "\\n", $str); 
	    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
	} 
	 
	/*// Excel file name for download 
	$fileName = "members-data_" . date('Y-m-d') . ".xls"; 
	 
	// Column names 
	$fields = array('Họ tên', 'Điện thoại'); 
	 
	// Display column names as first row 
	$excelData = implode("\t", array_values($fields)) . "\n"; 
	 
	// Fetch records
    $lineData = array('Lại Văn Phi Hùng', '0919165860'); 
    array_walk($lineData, 'filterData'); 
    $excelData .= implode("\t", array_values($lineData)) . "\n";
	 
	// Headers for download 
	// header("Content-Type: application/vnd.ms-excel"); 
	// header("Content-Disposition: attachment; filename=\"$fileName\""); 

	header("Content-Type: application/vnd.ms-excel; charset=UTF-8"); 
	header("Pragma: public"); 
	header("Expires: 0"); 
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Content-Type: application/force-download"); 
	header("Content-Type: application/octet-stream"); 
	header("Content-Type: application/download"); 
	header("Content-Disposition: attachment;filename=\"$fileName\""); 
	header("Content-Transfer-Encoding: binary"); 
	 
	// Render excel data 
	echo $excelData; 
	 
	exit;*/

/*$users = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Users.csv');
$output = fopen('php://output', 'w');
fputcsv($output, array('No', 'First Name', 'Last Name', 'Email'));

if (count($users) > 0) {
    foreach ($users as $row) {
        fputcsv($output, $row);
    }
}

fclose($output);*/

    function debug_arr($arr = null){
    	echo "<pre>";
    	print_r($arr);
    	echo "</pre>";
    }

    function get_data_by_id($html_base = '', $id = ''){
	    // $string = '<input type="text" autocomplete="" name="confirmedEmailAddress" id="emailaddress" maxlength="60" value="fname.lname@gmail.com" class="txt-box  txtbox-m "  aria-describedby="" aria-required="true" disabled="disabled" />';
	    $string = $html_base->find("#$id", 0)->outertext;
		$pattern = '/<input(?:.*?)id=\"' . $id . '\"(?:.*)value=\"([^"]+).*>/i';
		preg_match($pattern, $string, $matches);

		return isset($matches[1]) ? $matches[1] : '';
    }

    function get_data_page($url = ''){
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_REFERER, $url);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$headers = array(
			"Cookie: ci_session=" . CI_SESSION,
		);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    $str = curl_exec($curl);
	    curl_close($curl);

	    $html_base = new simple_html_dom();
	    $html_base->load($str);
	    $table = $html_base->find('.table', 0);

	    $theData = array();
	    $j = 0;
	    foreach($table->find('tr') as $row) {
	        if($j != 0){
	            $rowData = array();
	            $i = 0;
	            foreach($row->find('td') as $cell) {
	                if($i == 2){
	                    $temp_full_name = trim(strip_tags($cell->innertext));
	                    $rowData['full_name'] = substr($temp_full_name, 0, strpos($temp_full_name, "  Đổi "));
	                }
	                if($i == 3){
	                    $rowData['phone'] = trim(strip_tags($cell->innertext));
	                }
	                $i++;
	            }
	            $theData[] = $rowData;
	        }
	        $j++;
	    }
	    $html_base->clear(); 
	    unset($html_base);

	    return $theData;
    }

    $users = [];
    $per_page = 9;
    $max_page = 9180;
    $max_page = 500;
    $i = 0;
    while ($i <= $max_page) {
        $url = 'https://ecohappygo.com/admin/users';
        if($i != 0){
            $url .= '/' . $i;
        }
        // echo $url . "<br>";
        $users[] = get_data_page($url);
        $i += $per_page;
    }
    debug_arr($users);
    die;

    if(!isset($url)){
        // $url = 'http://thethao.vnexpress.net/photo/hau-truong/hom-nay-hoang-xuan-vinh-ve-nuoc-nguyen-tien-minh-quyet-dau-lin-dan-3452035.html';
        $url = 'https://ecohappygo.com';
        $url = 'https://ecohappygo.com/admin/users/content/8937';
        $url = 'https://ecohappygo.com/admin/users';
        $url = 'https://ecohappygo.com/admin/users/9';
    }
    // $html = file_get_html($url);
    // echo $html;

    //base url
    $base = $url;// 'https://play.google.com/store/apps';

	// $username = "admin";
	// $password = "Luong6789";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $base);
    curl_setopt($curl, CURLOPT_REFERER, $base);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);

 //    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	// curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	$headers = array(
		"Cookie: ci_session=" . CI_SESSION,
	);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $str = curl_exec($curl);
    curl_close($curl);

    // Create a DOM object
    $html_base = new simple_html_dom();
    // Load HTML from a string
    $html_base->load($str);

    $table = $html_base->find('.table', 0);
    // echo $table;

    // $abc = trim(" KHƯƠNG ANH TUẤN  Đổi Hoàng Doãn Bảo Trung");
    // $variable = substr($abc, 0, strpos($abc, "  Đổi "));
    // var_dump($variable);

    // initialize empty array to store the data array from each row
    $theData = array();

    // loop over rows
    $j = 0;
    foreach($table->find('tr') as $row) {
        if($j != 0){
            // initialize array to store the cell data from each row
            $rowData = array();
            $i = 0;
            foreach($row->find('td') as $cell) {
                // push the cell's text to the array
                // $rowData[] = $cell->innertext;
                if($i == 2){
                    $temp_full_name = trim(strip_tags($cell->innertext));
                    $rowData['full_name'] = substr($temp_full_name, 0, strpos($temp_full_name, "  Đổi "));
                }
                if($i == 3){
                    $rowData['phone'] = trim(strip_tags($cell->innertext));
                }
                $i++;
            }

            // push the row's data array to the 'big' array
            $theData[] = $rowData;
        }
        $j++;
    }
    debug_arr($theData);


    // $full_name = get_data_by_id($html_base, "full_name");
    // echo $full_name . "<br>";

    // $phone = get_data_by_id($html_base, "phone");
    // echo $phone;

    // //get all category links
    // foreach($html_base->find('a') as $element) {
    //     echo "<pre>";
    //     print_r( $element->href );
    //     echo "</pre>";
    // }

    $html_base->clear(); 
    unset($html_base);
?>