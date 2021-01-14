<?php

require './easy_bitbankcc/EasyBitbankcc.php';
require './vendor/autoload.php';
require './Env.php';

use seiyaan\bitbankcc\EasyBitbankcc;
use seiyaan\trade\Env;

$options = getopt(null, ['pair::', 'amount_price::', 'side::', 'n::', 'precision::', 'cancel::']);
$pair = $options["pair"] ?? "btc_jpy";
$amount_price = $options["amount_price"] ?? 10000;
$side = $options["side"] ?? "buy";
$n = $options["n"] ?? 5;
$precision = $options["precision"] ?? 4;
$flg_all_order_cancel = ($options["cancel"] ?? "0") === "1";

$easyBitbankcc = new EasyBitbankcc(Env::get("BITBANK_API_KEY"), Env::get("BITBANK_API_SECRET"));

if($flg_all_order_cancel){
    $activeOrders = $easyBitbankcc->getActiveOrders($pair);
    $order_ids = [];
    foreach($activeOrders["data"]["orders"] as $order) {
        if($order["side"] === $side) {
            $order_ids[] = $order["order_id"];
        }
    }
    if(count($order_ids) > 0) {
        $easyBitbankcc->postCancelOrders(["pair" => $pair, "order_ids" => $order_ids]);
    }
}

$depth_price = $easyBitbankcc->getDepthPrice($pair, $side === "buy" ? "bids" : "asks", $n);
$amount = round($amount_price / $depth_price, $precision);

// 注文を出す
$params = [
    'pair' => $pair,
    'amount' => $amount,
    'price' => $depth_price,
    'side' => $side, // 買い:buy 売り:sell
    'type' => 'limit' // 指し値:limit 成り行き:market
];

$order_result = $easyBitbankcc->postPrivateUserSpotOrder($params);

echo json_encode($order_result);

