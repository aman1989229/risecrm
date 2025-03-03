<div style="max-width: 1000px; margin: auto; color: #444;">
    <table style="">
        <tr>
            <td style="width: 50%;">

                <div style="width:10%;">
                    <img src="<?php echo get_file_uri(get_setting("system_file_path") . get_setting("invoice_logo")); ?>" />
                </div>
                <div><b><?php echo get_setting("company_name"); ?></b></div>
                <span style="font-size: 90%; color: #666;">
                    <div><?php
                        $company_address = nl2br(get_setting("company_address"));
                        $company_phone = get_setting("company_phone");
                        $company_website = get_setting("company_website");
                        if ($company_address) {
                            echo $company_address;
                        }
                        ?>
                        <?php if ($company_phone) { ?>
                            <div style="line-height: 25px"><?php echo lang("phone") . ": " . $company_phone; ?></div>
                        <?php } ?>
                        <?php if ($company_website) { ?>
                            <div  style="line-height: 7px"><?php echo lang("website"); ?>: <a style="color:#666; text-decoration: none;" href="<?php echo $company_website; ?>"><?php echo $company_website; ?></a></div>
                        <?php } ?>
                    </div>
                </span>
            </td>
            <td style="width: 15%;">
            </td>
            <td style="width: 35%;">
                <div style="text-align: right; line-height: 23px;">
                    <span style="font-size:20px; font-weight: bold; background-color: #70d467; color: #fff;">&nbsp; <span style="text-transform: uppercase;"><?php echo lang("invoice") . " #" . $invoice_info->id; ?></span>&nbsp; </span>
                    <div style="line-height: 10px;"></div>
                    <span><?php echo lang("bill_date") . ": " . $invoice_info->bill_date; ?></span><br />
                    <span><?php echo lang("due_date") . ": " . $invoice_info->due_date; ?></span>
                </div>
                <br /><br />
                <div>
                    <div style="line-height: 1px;"><strong><?php echo lang("bill_to"); ?></strong></div>
                    <div style="border-bottom: 1px solid #f2f2f2; line-height:1px;"></div><br />
                    <div style="line-height:2px;"><strong><?php echo $client_info->company_name; ?> </strong></div>
                    <span style="font-size: 90%; color: #666;">
                        <?php if ($client_info->address) { ?>
                            <div><?php echo nl2br($client_info->address); ?>
                                <?php if ($client_info->city) { ?>
                                    <br /><?php echo $client_info->city; ?>
                                <?php } ?>
                                <?php if ($client_info->state) { ?>
                                    <br /><?php echo $client_info->state; ?>
                                <?php } ?>
                                <?php if ($client_info->zip) { ?>
                                    <br /><?php echo $client_info->zip; ?>
                                <?php } ?>
                                <?php if ($client_info->country) { ?>
                                    <br /><?php echo $client_info->country; ?>
                                <?php } ?>
                                <?php if ($client_info->vat_number) { ?>
                                    <br /><?php echo lang("vat_number") . ": " . $client_info->vat_number; ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </span>
                </div>  

            </td>
        </tr>
    </table>
</div>

<table style="width: 100%; color: #444;">            
    <tr style="font-weight: bold; background-color: #087cb1; color: #fff;  ">
        <th style="width: 50%; border-right: 1px solid #eee;"> <?php echo lang("item"); ?> </th>
        <th style="text-align: center;  width: 15%; border-right: 1px solid #eee;"> <?php echo lang("quantity"); ?></th>
        <th style="text-align: right;  width: 15%; border-right: 1px solid #eee;"> <?php echo lang("rate"); ?></th>
        <th style="text-align: right;  width: 20%; "> <?php echo lang("total"); ?></th>
    </tr>
    <?php
    foreach ($invoice_items as $item) {
        ?>
        <tr style="background-color: #f4f4f4; ">
            <td style="width: 50%; border: 1px solid #fff; padding: 10px;"><?php echo $item->title; ?>
                <br />
                <span style="color: #888; font-size: 90%;"><?php echo $item->description; ?></span>
            </td>
            <td style="text-align: center; width: 15%; border: 1px solid #fff;"> <?php echo $item->quantity ." ".$item->unit_type; ?></td>
            <td style="text-align: right; width: 15%; border: 1px solid #fff;"> <?php echo '₹',' ';echo number_format($item->rate, $item->currency); ?></td>
            <td style="text-align: right; width: 20%; border: 1px solid #fff;"> <?php echo '₹',' ';echo number_format($item->total, $item->currency); ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="3" style="text-align: right;"><?php echo lang("total"); ?></td>
        <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;">
            <?php echo "₹"." ".$invoice_total_summary->invoice_subtotal, $invoice_total_summary->currency; ?>
        </td>
    </tr>
    <?php if ($invoice_total_summary->tax) { ?>
        <tr>
            <td colspan="3" style="text-align: right;"><?php echo $invoice_total_summary->tax_name; ?></td>
            <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;">
                <?php echo '₹',' ';echo number_format($invoice_total_summary->tax, $invoice_total_summary->currency); ?>
            </td>
        </tr>
    <?php } ?>
    <?php if ($invoice_total_summary->tax2) { ?>
        <tr>
            <td colspan="3" style="text-align: right;"><?php echo $invoice_total_summary->tax_name2; ?></td>
            <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;">
                <?php echo '₹',' ';echo number_format($invoice_total_summary->tax2, $invoice_total_summary->currency); ?>
            </td>
        </tr>
    <?php } ?>
    <?php if ($invoice_total_summary->total_paid) { ?>     
        <tr>
            <td colspan="3" style="text-align: right;"><?php echo lang("paid"); ?></td>
            <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;">
                <?php echo '₹',' ';echo number_format($invoice_total_summary->total_paid, $invoice_total_summary->currency); ?>
            </td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="3" style="text-align: right;"><?php echo lang("balance_due"); ?></td>
        <td style="text-align: right; width: 20%; background-color: #087cb1; color: #fff;">
            <?php echo '₹',' ';echo number_format($invoice_total_summary->balance_due, $invoice_total_summary->currency); ?>
        </td>
    </tr>
</table>

<br />
<br />
<div style="border-top: 2px solid #f2f2f2; color:#444;">
    <div><?php echo nl2br($invoice_info->note); ?></div>
</div>


