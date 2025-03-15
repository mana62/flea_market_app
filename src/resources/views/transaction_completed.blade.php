<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <p>{{ $chatRoom->seller->name }} さま</p>
    <p>いつもご利用いただきありがとうございます。</p>

    <p>以下の商品が取引完了しました。</p>

    <p><strong>商品名：</strong> {{ $chatRoom->item->name }}</p>
    <p><strong>価格：</strong> ¥{{ number_format($chatRoom->item->price) }}</p>

    <p>ご購入者様への評価をお願いします。</p>
    <div>
      <a href="{{ route('login') }}">ログインする</a>
    </div>
</body>
</html>
