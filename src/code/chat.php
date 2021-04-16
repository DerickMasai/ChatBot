<?php
  session_start();
  require "../../../pdo/chatbot_connect.php";

  // Removes special chars.
  function clean($message) {
    $message = strtolower($_POST['userMessage']);
    return preg_replace('/[^A-Za-z0-9\-]/', '', $message);
  }

  $message = clean($message);

  $keywordFound = false;

  if (isset($message) && !empty($message)) {
    $wordsInMessageArray = explode(' ', $message);

    // Grab all keywords in DB
    $sql = "SELECT keywords FROM responses";
  	$stmt = $PDO->prepare($sql);
  	$stmt->execute();
    $results = $stmt->fetchAll();
    $count = $stmt->rowCount();

    // Initialize keywords array
    $keywordsArray = [];

    // Add keywords in DB to keywords array
    for ($x = 0; $x <= $count; $x++) {
      array_push($keywordsArray, $results[$x]['keywords']);
    }

    foreach ($wordsInMessageArray as $word) {
      // Check if current word in looping $wordsInMessageArray is in $keywordsArray
      if (in_array($word, $keywordsArray)) {
        $word = '%' . $word . '%';
        $sql = "SELECT * FROM responses WHERE keywords LIKE ?";
        $stmt = $PDO->prepare($sql);
        $stmt->execute([$word]);
        $result = $stmt->fetchAll();
        echo $result[0]['reply'];

        $keywordFound = true;

        break;
      }
    }
  } else {
    echo "I help best when I get your question first 😁.";
  }

  if ($keywordFound == false) {
    echo "🤔 Hmm, I can't seem to answer this at this time. I'll forward it to my creator. Can I assist in any other way?";
  }
?>
