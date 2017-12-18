# php-layout-class

テンプレートファイルを使わずに、クラスで各ページを定義する。

この設計はLayout, Page, PageInfoクラスから成り立つ。Layoutのみ抽象クラスである。3つの役割のクラスを用意すればよく、実装方法に縛りはない。役割のみルール化しており、実装は自由である。下記はその一例である。

`php index.php`でサンプルを実行できる。

## アクションメソッドでのPageクラスの使用例

下記は4種類のPageの出力方法。アクションメソッドではpsr-7のResponseオブジェクトを返すことが望ましい。

```php
<?php

class MyController extends BaseController
{
    public function actionMethod()
    {
        // do something
        
        // Page情報のレンダリングを外部に任せる
        return new UserPage($user);
        
        // その場でレンダリングしてしまう
        echo new UserPage($user);
        
        // psr-7のResponseオブジェクトに書き込む
        $response->getBody()->write((string)new UserPage($user));
        return $response;
        
        // bodyの書き込みをヘルパーメソッドでラップするのも良い
        return $response->setPage(new UserPage($user));
    }
}
```

## Page

PageはLayoutを継承し、差分のHTMLソースをメソッド内で記述。Layoutは一番上の親クラスになる。各ページごとにPageを用意するため、アクションメソッドと対になるのが望ましい。

アクションメソッドからDBからの結果であるモデルなど、動的な値を渡す場合はコンストラクタ経由にする。値の外部注入をコンストラクタで完結する、完全コンストラクタが望ましい。

```php
<?php

final class UserPage extends DefaultLayout
{
    /** @var User */
    private $user;
    
    public function __construct(User $user)
    {
        // 親クラスのpage情報を変えたい場合は、pageのプロパティを上書きすれば良い。
        $this->page = new PageInfo();
        $this->page->setPageTitle('user page');
        $this->user = $user;
    }

    protected function content(): void
    {
        ?>
        <p>Your name is <?= $this->user->name() ?></p>
        <?
    }

    protected function sidebar(): void
    {
        // デフォルトのサイドバーの直後に追加している例。
        // 全体を書き換える場合は、親のメソッドを呼ぶ必要はない。
        parent::sidebar();
        ?>
        add user sidebar
        <?
    }
}
```

## Layout

Layoutは`__toString`の実装と、page情報が必須。page情報は、種類ごとにプロパティを定義しても良いが、拡張性を考えると専用クラスを経由すると良い。今回はPageInfoを作成。

これにより、Layoutにキャッシュなどテンプレートとしての機能を追加する際に、ページ情報を気にせずプロパティを定義できる。

```php
<?php

abstract class Layout
{
    /** @var PageInfo */
    protected $page;

    abstract public function __toString(): string;

    // レイアウトごとに実装方法を変えたい場合は、子クラスで抽象を定義してもよい。
    abstract protected function content(): void;
}
```

ベースとなるLayoutを継承して、実際に使用するLayoutを定義していく。`__toString()`がHTMLを返せば良い。子クラスとなるpageで書き換えたい部分は、Layoutでメソッド抽出してPageでオーバーライドする。下記は`content()`と`sidebar()`をpageで変更可能にしている。

先にコンテンツ部分からレンダリングをするようにしているため、コンテンツ部分のJavascriptの出力をfooterにまとめたりも可能。内側からレンダリングされるということ。

```php
<?php

abstract class DefaultLayout extends Layout
{
    public function __toString(): string
    {
        ob_start();
        $this->content();
        $content = ob_get_clean();
        
        ob_start();
        ?>
        <html>
        <head>
            <title><?= $this->page->title() ?></title>
        </head>
        <body>
        <div class="content">
            <?= $content ?>
        </div>
        <div class="sidebar">
            <? $this->sidebar() ?>
        </div>
        </body>
        </html>
        <?
        return ob_get_clean();
    }

    protected function sidebar(): void
    {
        ?>
        sidebar content
        <?
    }
}
```

## PageInfo

HTMLをブロックごと上書きする場合は、Pageクラスで親のLayoutのメソッドをオーバーライドすれば良い。ブロック内で取得するページ情報を上書きする場合は、PageInfoのプロパティを書き換える。Layout, Pageクラスはページ情報をハードコードせずに、PageInfoを経由しておく必要がある。

格納する値は、主にmetaタグの値になるだろう。

```php
<?php

class PageInfo
{
    /** @var string */
    private $baseTitle = 'My Site';

    /** @var string */
    private $pageTitle;

    public function title(): string
    {
       return "{$this->pageTitle} - {$this->baseTitle}";
    }

    public function pageTitle(): string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }
}
```
