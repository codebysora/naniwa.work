<?php
mb_language("ja");
mb_internal_encoding("UTF-8");
ini_set('display_errors', 1);

	$error = '';

	if($_POST['mode']=='send') {

		session_start();



		if($_SESSION['last_token'] != $_POST['_token']) {
			// 送信処理
			$support_addr = 'info@naniwa-co.jp';

			$mailto = $_POST['email'];
			$subject = "フォームの自動返信メール";

			$__comp = (@$_POST['company']) ? sprintf("【会社名】%s%s", $_POST['company'], PHP_EOL) : '';
			$__kana = (@$_POST['kana']) ? sprintf("【ふりがな】%s 様%s", $_POST['kana'], PHP_EOL) : '';
			$__tel = (@$_POST['tel']) ? sprintf("【電話番号】%s %s", $_POST['tel'], PHP_EOL) : '';

			$content = <<<TEXT
{$_POST['name']}  様


この度は、株式会社ナニワのWEBサイトよりお問い合わせをいただき、誠にありがとうございます。

お問合せいただい内容は次の通りです。
担当者より、あらためてご連絡させて頂きますので、今しばらくお待ち下さいませ。
3日ほど経ってもご連絡が無い場合はお手数ですが下記アドレスまたは電話番号まで改めてご連絡下さいませ。

E-mail: info@naniwa-co.jp
TEL: 03-3654-2474

{$__comp}【氏名】{$_POST['name']} 様
{$__kana}{$__tel}【メールアドレス】{$_POST['email']}
【お問合わせ内容】
{$_POST['content']}
TEXT;

			// 2.差出人を日本語表示
			$mailfrom = sprintf("From:%s<info@naniwa-co.jp>\nBcc:{$support_addr}", mb_encode_mimeheader("株式会社ナニワ"));

			// 3.上記（送信先、件名、本文、差出人）を日本語でメール送信実行
			mb_send_mail($mailto, $subject, $content, $mailfrom);

// 管理者への送信
			$ua = $_SERVER['HTTP_USER_AGENT'];
			$_prefix = 'PC';
			if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
				// スマートフォンからアクセスされた場合
				$_prefix = 'SP';
			} elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
				// タブレットからアクセスされた場合
				$_prefix = 'TABLET';
			} elseif ((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'KDDI') !== false) || (strpos($ua, 'SoftBank') !== false) || (strpos($ua, 'Vodafone') !== false) || (strpos($ua, 'J-PHONE') !== false)) {
				// 携帯からアクセスされた場合
				$_prefix = 'ガラケー';
			}
			$subject = "{$_prefix}サイトからお問合わせがありました";

			$content = <<<TEXT
お問合わせ内容

{$__comp}【氏名】{$_POST['name']} 様
{$__kana}{$__tel}【メールアドレス】{$_POST['email']}
【お問合わせ内容】
{$_POST['content']}
TEXT;

			$mailfrom = sprintf("From:%s<noreply@naniwa-co.jp>", mb_encode_mimeheader("HPお問合わせ"));
			mb_send_mail($support_addr, $subject, $content, $mailfrom);

			$_SESSION['last_token'] = $_POST['_token'];
			header('location: thanks.html');
			exit;
		}
		else {
			// 二重送信NG
			$error = 'ブラウザの「戻るボタン」等での二重送信はしないでください。';
		}
	}


?><!DOCTYPE html>
<html lang="ja">
<head>

	<?php include 'ssi/pre.content.html'; ?>

	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<title>お問合わせ | 株式会社ナニワ</title>
	<meta name="keywords" content="株式会社ナニワ" />
	<meta name="description" content="業務のご依頼や採用の応募など、必要事項にご入力の上ご送信下さい。" />
	<meta name="format-detection" content="telephone=no">

	<?php include 'ssi/meta.html'; ?>
	<!-- ページ固有スタイル -->
	<link rel="stylesheet" href="css/contact.css" />

</head>


<body>


<div id="page">

	<?php include 'ssi/header.html'; ?>

	<main class="form">

		<div class="wrap">

			<nav class="breadcrumb">
				<ul>
					<li><a href="index.html">トップ</a></li>
					<li>お問合わせ</li>
				</ul>
			</nav>

			<h2 align="center"><img src="img/contact/contact_title@2x.png" alt="お問合わせタイトル" width="250" /></h2>

			<form action="" method="post">

<?php if(@$_POST['mode']=='confirm'): ?>
			<p class="desc">内容をご確認頂き、<br class="sp" />問題無ければ「送信」ボタンを押して下さい。</p>

<input type="hidden" name="type" value="<?php echo(htmlspecialchars(@$_POST['type'])) ?>" />
<input type="hidden" name="company" value="<?php echo(htmlspecialchars(@$_POST['company'])) ?>" />
<input type="hidden" name="name" value="<?php echo(htmlspecialchars(@$_POST['name'])) ?>" />
<input type="hidden" name="kana" value="<?php echo(htmlspecialchars(@$_POST['kana'])) ?>" />
<input type="hidden" name="tel" value="<?php echo(htmlspecialchars(@$_POST['tel'])) ?>" />
<input type="hidden" name="email" value="<?php echo(htmlspecialchars(@$_POST['email'])) ?>" />
<input type="hidden" name="content" value="<?php echo(htmlspecialchars(@$_POST['content'])) ?>" />
<input type="hidden" name="_token" value="<?php echo md5(uniqid(rand(), 1)); ?>" />

			<dl>
				<dt>業務形態</dt>
				<dd><span><?php echo(htmlspecialchars(@$_POST['type'])) ?></span></dd>
				<dt>会社名</dt>
				<dd><span><?php echo(htmlspecialchars(@$_POST['company'])) ?></span></dd>
				<dt class="required">氏名</dt>
				<dd><span><?php echo(htmlspecialchars(@$_POST['name'])) ?></span></dd>
				<dt>ふりがな</dt>
				<dd><span><?php echo(htmlspecialchars(@$_POST['kana'])) ?></span></dd>
				<dt>電話番号</dt>
				<dd><span><?php echo(htmlspecialchars(@$_POST['tel'])) ?></span></dd>
				<dt class="required">メールアドレス</dt>
				<dd><span><?php echo(htmlspecialchars(@$_POST['email'])) ?></span></dd>
				<dt class="required">お問合わせ内容</dt>
				<dd><span><?php echo(nl2br(htmlspecialchars(@$_POST['content']))) ?></span></dd>
			</dl>

			<p align="center">
				<button type="submit" class="btn back" name="mode" value="back">戻る</button>
				<button type="submit" class="btn send" name="mode" value="send">送信</button>
			</p>
<?php else: ?>


<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/localization/messages_ja.min.js"></script>

<script>
$(function(){
	$("input"). keydown(function(e) {
		if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
			e.preventDefault();

			// 現在フォーカスしている入力欄からフォーカスを外すことで、キーボードを強制的に閉じる
			document.activeElement.blur();
			$('input').blur();
		}
		else {
			return true;
		}
	});
});
</script>

<script>
/*
$.extend( $.validator.messages, {
	required: "このフィールドは必須です。",
	remote: "このフィールドを修正してください。",
	email: "有効なEメールアドレスを入力してください。",
	url: "有効なURLを入力してください。",
	date: "有効な日付を入力してください。",
	dateISO: "有効な日付（ISO）を入力してください。",
	number: "有効な数字を入力してください。",
	digits: "数字のみを入力してください。",
	creditcard: "有効なクレジットカード番号を入力してください。",
	equalTo: "同じ値をもう一度入力してください。",
	extension: "有効な拡張子を含む値を入力してください。",
	maxlength: $.validator.format( "{0} 文字以内で入力してください。" ),
	minlength: $.validator.format( "{0} 文字以上で入力してください。" ),
	rangelength: $.validator.format( "{0} 文字から {1} 文字までの値を入力してください。" ),
	range: $.validator.format( "{0} から {1} までの値を入力してください。" ),
	step: $.validator.format( "{0} の倍数を入力してください。" ),
	max: $.validator.format( "{0} 以下の値を入力してください。" ),
	min: $.validator.format( "{0} 以上の値を入力してください。" )
});
*/
$.extend( $.validator.messages, {
	required: "{1}を入力してください。",
	remote: "このフィールドを修正してください。",
	email: "有効なEメールアドレスを入力してください。",
	url: "有効なURLを入力してください。",
	date: "有効な日付を入力してください。",
	dateISO: "有効な日付（ISO）を入力してください。",
	number: "有効な数字を入力してください。",
	digits: "数字のみを入力してください。",
	creditcard: "有効なクレジットカード番号を入力してください。",
	equalTo: "同じ値をもう一度入力してください。",
	extension: "有効な拡張子を含む値を入力してください。",
	maxlength: $.validator.format( "{0} 文字以内で入力してください。" ),
	minlength: $.validator.format( "{0} 文字以上で入力してください。" ),
	rangelength: $.validator.format( "{0} 文字から {1} 文字までの文字数で入力してください。" ),
	range: $.validator.format( "{0} から {1} までの値を入力してください。" ),
	step: $.validator.format( "{0} の倍数を入力してください。" ),
	max: $.validator.format( "{0} 以下の値を入力してください。" ),
	min: $.validator.format( "{0} 以上の値を入力してください。" )
});
</script>

<script>
var is_confirm = false;

$(function() {
	$('.btn.confirm').on("click", function() {
		is_confirm = true;
	});
});

$("form").validate({
	rules: {
		company: {
			rangelength: [1, 200]
		},
		name: {
			required: [true, '氏名'],
			rangelength: [1, 200]
		},
		tel: {
/*
			number: [true, '電話番号は番号のみ入力してください'],
			maxlength: [20]
*/
		},
		email: {
			required: [true, 'メールアドレス'],
			email: true
		},
		content: {
			required: [true, 'お問合わせ内容'],
			rangelength: [10, 10000]
		},
		type: {
			required: [true, '業務形態']
		},
		company: {
			required: '#type1:checked'
		}
	},
	messages: {
		company: { required: '業務形態が法人の場合は入力してください。' }
	},
	//エラーメッセージ出力箇所調整
	errorPlacement: function(error, element){
		if (element.is(':radio')) {
			error.appendTo(element.parents('dd'));
		} else {
			error.insertAfter(element);
		}
	},
/*
	invalidHandler:function(e,validator) {
		var errors = validator.numberOfInvalids();

		if(errors) {
			$('p.desc').hide();
			$('p.error').text(errors + '個のエラーがあります。').show();
		}
		else {
			$('p.desc').show();
			$('p.error').hide();
		}
	}
*/
	focusInvalid: false,
	showErrors: function(errorMap, errorList) {
		if(this.numberOfInvalids()) {
			$('p.desc').hide();
			$('p.error').text(this.numberOfInvalids() + '個のエラーがあります。').show();

			if(is_confirm) {
				$(window).scrollTop($('p.error').offset().top - 60);
				is_confirm = false;
			}
		}
		else {
			$('p.desc').show();
			$('p.error').hide();
		}
		this.defaultShowErrors();
	}
});
</script>


			<p class="desc">お問合わせは下記フォームにご入力頂き、<br class="sp" />「内容確認」ボタンを押して下さい。</p>

			<p class="error"><?php echo(htmlspecialchars($error)); ?></p>

			<dl>
				<dt class="required">業務形態</dt>
				<dd>
					<label><input type="radio" name="type" id="type1" value="法人" <?php echo(@$_REQUEST['type']=='法人' ? 'checked' : '') ?> />法人</label>
					<label><input type="radio" name="type" id="type2" value="個人" <?php echo(@$_REQUEST['type']=='個人' ? 'checked' : '') ?> />個人</label>
				</dd>
				<dt>会社名(法人のお客様)</dt>
				<dd><input type="text" name="company" value="<?php echo(htmlspecialchars(@$_REQUEST['company'])) ?>" /></dd>
				<dt class="required">氏名</dt>
				<dd><input type="text" name="name" value="<?php echo(htmlspecialchars(@$_REQUEST['name'])) ?>" /></dd>
				<dt>ふりがな</dt>
				<dd><input type="text" name="kana" value="<?php echo(htmlspecialchars(@$_REQUEST['kana'])) ?>" /></dd>
				<dt>電話番号</dt>
				<dd><input type="tel" name="tel" value="<?php echo(htmlspecialchars(@$_REQUEST['tel'])) ?>" /></dd>
				<dt class="required">メールアドレス</dt>
				<dd><input type="email" name="email" value="<?php echo(htmlspecialchars(@$_REQUEST['email'])) ?>" /></dd>
				<dt class="required">お問合わせ内容</dt>
				<dd><textarea name="content" ><?php echo(htmlspecialchars(@$_REQUEST['content'])) ?></textarea></dd>
			</dl>

			<p align="center">
				<button type="submit" class="btn confirm" name="mode" value="confirm">内容確認</button>
			</p>
<?php endif; ?>
			</form>

		</div>
	</main>

	<?php include 'ssi/footer.html'; ?>

</div>

<?php include 'ssi/post.content.html'; ?>

</body>
</html>