<?php
  $arglist = implode(", ", array_map(function($arg){
    return '$'.$arg;
  }, $args));
?>
  // <?=$name?>
  
  public function <?=$name?>(<?=$arglist?>){
    return $this-><?=$httpMethod?>("<?=$name?>", array(
      <?php foreach($args as $arg): ?>
        "<?=$arg?>" => $<?=$arg?>,
      <?php endforeach; ?>
    ));
  }

