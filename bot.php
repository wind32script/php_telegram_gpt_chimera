<?php 

//$data = file_get_contents('php://input');
$data = json_decode(file_get_contents('php://input'), TRUE);
file_put_contents('message.txt', print_r($data, true));



define('TOKEN', '623:AA8G4');
// Функция вызова методов API.

function get_gpt($msg, $id)
{
    // #gpt-3.5-turbo gpt-4-poe gpt-4-32k-poe

	$url = 'https://chimeragpt.adventblocks.cc/api/v1/chat/completions';
	$apikey = 'cvjf5U5rcmq9F508p5';
	$data = array(
		"model" => "gpt-4",
		"messages" => array(
			array(
				"role" => "assistant",
				"content" => $msg,
				"name" => $id
			)
		)
	);
	
	$payload = json_encode($data);
	
	
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Bearer ' . $apikey));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	$response = curl_exec($ch);
	curl_close($ch);
	file_put_contents('gpt_input.txt', $msg);	
	file_put_contents('gpt_output.txt', print_r($response, true));
	$response_data = json_decode($response, true);
	if ( strpos($response, 'Bad context detected!')>1)
	    {
	      return 'Я не могу обсуждать эту тему.';  
	    }

	 $output = $response;
	 $output = $response_data;
	 $output = $response_data['choices'][0]['message']['content'];
	 return  $output;

	
}

function sendTelegram($method, $response)
{
        $ch = curl_init('https://api.telegram.org/bot' . TOKEN . '/' . $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
}


if (!empty($data['message']['text'])) {
        $text = $data['message']['text'];
        
        sendTelegram('sendMessage',array('chat_id' => $data['message']['chat']['id'],'text' => 'Ожидайте ответ, я подумаю...'));
        $otvet=get_gpt(   $data['message']['text'] , data['message']['chat']['id'] );
        

        if ($otvet=='')
            {
                      sendTelegram('sendMessage',array('chat_id' => 8393,'text' => $data['message']['chat']['id'].';' .$data['message']['chat']['username'].';'.$data['message']['chat']['first_name']. ' msg:'.$data['message']['text'] )); 
                      
                      $gptu= file_get_contents("gpt_output.txt");
                     sendTelegram('sendMessage',array('chat_id' => 839,'text' =>  $gptu)); 
                     
                      sendTelegram('sendMessage',array('chat_id' => $data['message']['chat']['id'],'text' => 'Какая то ошибка в запросе или в боте, сообщение отправлено разработчику')); 
                      
            }
        
        
        sendTelegram('sendMessage',array('chat_id' => $data['message']['chat']['id'],'text' => ''.$otvet)); 
        exit();
        
}



?>
