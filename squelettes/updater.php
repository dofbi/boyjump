<?php
session_start();
$nb  = 0;
if(isset($_SESSION['myplayliste']))
{
	$nb = count($_SESSION['myplayliste']);
}
$_SESSION['myplayliste'][$nb][url] = $_GET['url'] ;
$_SESSION['myplayliste'][$nb][titre] = $_GET['titre'];
$id='myplayliste_'.$nb;
?>
<a href="<?php echo $_GET['url'] ?>" id="<?php echo $id ?>" >
	<p><?php echo $_GET['titre'] ?></p>
	<p class="time">0:20 min</p>

<p><img src="supprimer1.jpg" onClick="javascript:supprimer('<?php echo $id ?>')"></p>
</a>