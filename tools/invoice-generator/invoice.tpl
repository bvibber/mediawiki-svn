<html>
<body>
<table border="1" cellpadding="5" cellspacing="0">
<tr>
<th colspan="6" align="center"><h2>INVOICE</h2>
<?= $address ?>
<br/><br/>
Phone: <?= $phone ?>
</th>
</tr>
<tr>
<td colspan="3" valign="top"><b>Sold to:</b><br />
<?= $soldTo ?>
<td colspan="3" valign="top"><b>Ship to:</b><br />
</tr>
<tr align="center" style="background:lightgrey;">
<td colspan="4">&nbsp;</td>
<td>Invoice No.</td>
<td>Issue date</td>
</tr>

<tr align="center">
<td colspan="4">&nbsp;</td>
<td><?= $invoiceNumber ?></td>
<td><?= $date ?></td>
</tr>

<tr><td colspan="6">&nbsp;</td></tr>

<tr style="background:lightgrey;">
<td>Quantity</td>

<td colspan="3">Description</td>
<td align="right">Price Each</td>
<td align="right">Amount</td>
</tr>
<?php 
$sum = 0;
foreach ( $items as $item ):
$sum += $item['amount'];
?>
<tr>
<td><?= $item['quantity'] ?></td>
<td colspan="3"><?= $item['description'] ?></td>
<td align="right"><?= $item['priceEach'] ?> <?= $currency ?></td>
<td align="right"><?= $item['amount'] ?> <?= $currency ?></td>
</tr>
<?php endforeach; ?>
<td></td>
<td colspan="3" align="right"><b>Invoice Total</b></td>
<td></td>
<td align="right"><?= $sum ?> <?= $currency ?></td>

</tr>
</table>
</body>
</html>
