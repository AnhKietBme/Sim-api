<?php
require_once("./vendor/autoload.php");
use tgMdk\dto\CvsAuthorizeRequestDto;
use tgMdk\TGMDK_Transaction;

// Set the content type to JSON
header('Content-Type: application/json');

// Define your API routes and their corresponding handlers
$routes = [
    '/api/data' => function() {

        $request_data = new CvsAuthorizeRequestDto();

        $request_body = json_decode(file_get_contents('php://input'));
        $serviceOptionType = $request_body->serviceOptionType;
        $orderId = $request_body->orderId;
        $amount = $request_body->amount;
        $name1 = $request_body->name1;
        $name2 = $request_body->name2;
        $telNo = $request_body->telNo;
        $payLimit = $request_body->payLimit;
        $paymentType = $request_body->paymentType;
        $pushUrl = $request_body->pushUrl;
        
        // Create CvsAuthorizeRequestDto object with retrieved parameters
        $request_data = new CvsAuthorizeRequestDto();
        $request_data->setServiceOptionType($serviceOptionType);
        $request_data->setOrderId($orderId);
        $request_data->setAmount($amount);
        $request_data->setName1($name1);
        $request_data->setName2($name2);
        $request_data->setTelNo($telNo);
        $request_data->setPayLimit($payLimit);
        $request_data->setPaymentType($paymentType);
        $request_data->setPushUrl($pushUrl);

        // Process the request with TGMDK_Transaction
        $transaction = new TGMDK_Transaction();
        $response_data = $transaction->execute($request_data);
        
        // Get response: https://www.veritrans.co.jp/docs/cvs.html#section2-1
        $txn_status = $response_data->getMStatus();
        $txn_result_code = $response_data->getVResultCode();
        $error_message = mb_convert_encoding($response_data->getMerrMsg(), "UTF-8");
        $haraikomi_url = $response_data->getHaraikomiUrl();


        $data = ['result_code' => $txn_result_code, 'status' => $txn_status, 'message' => $error_message, 'haraikomiUrl' => $haraikomi_url];
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    },
];

// Get the requested URI
$request_uri = $_SERVER['REQUEST_URI'];

// Check if the requested route exists
if (array_key_exists($request_uri, $routes)) {
    // Call the corresponding handler for the route
    $routes[$request_uri]();
} else {
    // Return a 404 response if the route is not found
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
?>
