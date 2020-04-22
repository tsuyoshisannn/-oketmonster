<?php
ini_set('log_errors','on'); //ログをとるか
ini_set('error_log','php.log'); //ログの出力ファイルを指定
session_start(); //セッション使う

// オブジェクト生成コーナー

// ============================================
//  モンスター格納用 
$monsters = array();
// ============================================


//   説明  =================================================================================

    // 抽象クラスは、複数のクラスの、共通処理の中に、一部違う処理が入る場合などに使用する。
    // 直接インスタンスを生成できないクラス,抽象クラスには抽象メソッドが定義できる。

    // 抽象メソッドとは処理内容を持たずに名前だけ定義されたメソッド
    // 抽象メソッドは継承先のクラスで必ずオーバーライドしないとエラーになる

    // 抽象クラスは「クラスを作るクラス（設計図）」といった感じ
    // 抽象クラスはクラス同士で共通化できるところを抜き出したもの


    // 継承とはすでに存在するクラスを拡張する仕組み。(privateの付いているものは継承されない)
    // (↑継承する想定のクラスだが外部には非公開にしたい場合はprotectedを使う)
    // 継承元のクラスを親クラス、継承先のクラスを子クラスという

    // オーバーライドとは、親クラスのメソッドを子クラスで書き換える仕組み
    // オーバーライドするには、親クラスと同じメソッドを子クラスで作って中に自由に書くだけ
    // 親クラスのメソッドの内容も子クラスで反映させたい時は親クラスのメソッドを呼ぶ

    // セッターとゲッターについて
    // セッターはプロパティの値をセット、設定するメソッドのこと(プロパティを直接操作しないでメソッドを介して操作)
    // ゲッターはゲットしてくるメソッド
    // セッターは'set○○(プロパティ名)'、ゲッターは'get○○(プロパティ名)'とするのが慣習！ その方がわかりやすい

    // private	    そのクラスからしかアクセスできない
    // protected	そのクラスと、サブクラス(クラスを継承して作成した子クラス )からしかアクセスできない
    // public	    どこからでもアクセスできる

// =========================================================================================


// ============================================
// 抽象クラス（生き物クラス）
// ============================================


abstract class Creature{
     protected $name; //名前
     protected $hp; //体力
     protected $attackMin; // 最大攻撃力
     protected $attackMax; // 最低攻撃力
     abstract public function sayCry(); // 叫び声を入れるやつ
     public function setName($str){
         $this->name = $str; // インスタンス(オブジェクト指向における実体)のプロパティを参照し代入
     }
     public function getName(){
         return $this->name; // 名前を引っ張ってきて表示するためのやつ
     }
     public function setHp($num){
         $this->hp = $num; // 体力を代入する用
     }
     public function getHp(){
         return $this->hp; // 体力情報を持ってくる
     }
     public function attack($targetObj){
         $attackPoint = mt_rand($this->attackMin, $this->attackMax); //攻撃力を設定
         if(!mt_rand(0,9)){  //10分の1でクリティカル
        $attackPoint = $attackPoint * 1.5; //クリティカルは攻撃力の1.5倍
        $attackPoint = (int)$attackPoint; //(int)とすることで整数にキャストしている
        History::set($this->getName().'のクリティカルヒット!!'); // ヒストリーは下に作る
        // '::(スコープ定義演算子というらしい)'はstatic, 定数 
        // およびオーバーライドされたクラスのプロパティやメソッドにアクセスすることができる
        }
        $targetObj->setHp($targetObj->getHp()-$attackPoint);
        History::set($attackPoint.'ポイントのダメージ!');
     }
}

// 体力最大値クラス
class MaxHp {
    const HUMAN = 500;
    const MONSTER = 200;
}

// 主人公クラス  (今回は一人だが、後々増やすことになったらクラス化した方がメンテナンス性がいいそう)
class Human extends Creature{ // extendsは継承のこと、今回は生き物クラスを継承している
    protected $maxhp;
    // プロパティ
    public function __construct($name, $maxhp, $hp, $attackMin, $attackMax) {
        // コンストラクタとは インスタンスを作成したタイミングで実行されるメソッドのこと
        // (クラスをnewした瞬間に実行される関数のこと)
        $this->name = $name;
        $this->maxhp = $maxhp;
        $this->hp = $hp;
        $this->attacMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    public function getMaxHp(){
        return $this->maxhp;
    }
    // 悲鳴
    public function sayCry(){
        History::set($this->name.'がさけぶ!');
        History::set('ぐはっ!!');
    }
}


// モンスタークラス
class Monster extends Creature{
// プロパティ (物体の特性・特質を意味する)
    protected $maxhp;
    protected $img;
    // コンストラクタ (クラスをnewした瞬間に実行される関数のこと)
    public function __construct($name, $maxhp, $hp, $img, $attackMin, $attackMax){
        $this->name = $name;
        $this->maxhp = $maxhp;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    // ゲッター
    public function getMaxHp(){
        return $this->maxhp; //体力最大値持ってくる
    }
    public function getImg(){
        return $this->img; //画像をもってくる
    }
    // 悲鳴
    public function sayCry(){
        History::set($this->name.'がさけぶ!');
        History::set('くっ!!!');
    }
}

// ====================================================
// 静的メンバ
// インスタンスを生成せずにアクセス可能なメンバを静的メンバという
// メンバとはクラスのプロパティ、メソッドのことをひっくるめた言い方
// いちいちインスタンス生成せずにすぐに使いたい便利機能などは静的メンバを使う
// 静的にしたいメンバは「public static」をつける
// インスタンス生成はクラスで定義したプロパティやメソッドをコピーしているイメージ
// 静的メンバを呼び出すには「クラス名::」を使う。「インスタンス名->」は使えない
// =====================================================

interface HistoryInterface{
    public static function set($str);
    public static function clear();
}



// ==========インターフェースの決まり======================
// 定義できるのは抽象メソッドのみ
// 抽象メソッドだが「abstract」はいらない
// アクセス修飾子には「public」しか指定できない
// 直接インスタンスの生成はできない
// インターフェースは多重継承できる
// =====================================================

// インスタンスは抽象メソッドのみ定義できるクラスのようなもの
// インターフェースを継承してクラスを定義することを「インターフェースを実装する」と言う
// インターフェースを実装するクラスには、「implements」を記述します。
// インターフェースを実装したクラスはインターフェースで宣言されている抽象メソッドをオーバーライドする必要があります。
// １つのクラスに複数のインターフェースを実装することができます。その場合カンマで区切って記述します。

// クラス内で別プロパティやメソッドのアクセスする際は「そのメンバが必ずある」前提となる為
// そのクラスが実装漏れしていればエラーになってしまう
// そこで、必ず実装してもらう為のクラスのテンプレートとしてインターフェースが使える

// 「インターフェース」は、異なる2つのものを仲介するという意味を持っています。


// ポリモーフィズム
// ポリモーフィズムとは異なる操作を同じ動作で実現するもの
// 実際の実装では、異なるクラスでも同じメソッドを使うことで実現させるもの
// 同じメソッドがある前提で処理をする為呼び出すクラス（インスタンス）にメソッドが必ずないとエラーとなってしまう
// 呼び出されるクラスでは、インターフェースを実装することで、必ずそのメソッドを実装することを強制できる


// 履歴管理クラス(インスタンス化して複数に増殖させる必要がないので、staticにする)
class History implements HistoryInterface{
    public static function set($str){
        // セッションhistoryが出来ていなければ作る
        if(empty($_SESSION['history'])) $_SESSION['history'] = '';
        // 文字列をセッションhistoryへ格納
        $_SESSION['history'] .= $str.'<br>';
    }
    public static function clear(){
        unset($_SESSION['history']);
    }
}

// インスタンス(オブジェクトを指す)作成
$human = new Human('主人公', MaxHp::HUMAN, 500, 40, 100);
$monsters[] = new Monster('ヤキトリ', MaxHp::MONSTER, 200, 'img/yakitori.JPG', 20, 40);
$monsters[] = new Monster('ヤバイクサ', MaxHp::MONSTER, 200, 'img/yabaikusa.JPG', 20, 60);
$monsters[] = new Monster('エジ', MaxHp::MONSTER, 200, 'img/eji.JPG', 30, 50);

// モンスター生成
function createMonster(){
    global $monsters;
    $monster = $monsters[mt_rand(0,2)];
    History::set($monster->getName().'があらわれた!');
    $_SESSION['monster'] = $monster;
}

// 主人公生成
function createHuman(){
    global $human;
    $_SESSION['human'] = $human;
}

// 初期化
function init(){
    History::clear();
    History::set('しょきかします!');
    $_SESSION['knockDownCount'] = 0;
    createHuman();
    createMonster();
}

// ゲームオーバー
function gameOver(){
    $_SESSION = array();
}

// post送信されていた場合
if(!empty($_POST)){
    $attackFlg = (!empty($_POST['attack'])) ? true : false;
    $startFlg = (!empty($_POST['start'])) ? true : false;
    error_log('POSTされた！');

    if($startFlg){
        init();
    }else{
        // 攻撃するを押した場合
        if($attackFlg){

            // モンスターに攻撃を与える
            History::set($_SESSION['human']->getName().'のこうげき!');
            $_SESSION['human']->attack($_SESSION['monster']);
            $_SESSION['monster']->sayCry();

            // モンスターが攻撃をする
            History::set($_SESSION['monster']->getName().'のこうげき!');
            $_SESSION['monster']->attack($_SESSION['human']);
            $_SESSION['human']->sayCry();

            // 自分のHPが0になったらゲームオーバー
            if($_SESSION['human']->getHp() <= 0){
                gameOver();
            }else{
                // HPが0以下になったら、別のモンスターを出現させる
                if($_SESSION['monster']->getHp() <= 0){
                    History::set($_SESSION['monster']->getName().'をたおした!');
                    createMonster();
                    $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
                }
            }
        }else{ //逃げるを押した場合
            History::set('うまくにげきれた!');
            createMonster();
            
        }
    }
    $_POST = array();
}

?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>○けもん？</title>
    <!-- cssファイル読み込み -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="js/jquery-3.5.0.min.js"></script>
</body>
</html>

  </head>
  <body>
      <h1 class="game-title">○けもん？</h1>

      <!-- スタート画面 -->
      <?php if (empty($_SESSION)){ ?>
        <h2 class="game-start">GAME START ?</h2>
        <form method="post" style="text-align: center;">
            <input type="submit" name="start" value="▶︎ゲームスタート">
        </form>
      <?php }else{ ?>

      

  <!-- ゲーム画面左 -->
   <div class="game-display">

  <!-- 敵の情報 -->
      <section class="enemy">
          <div class="enemy-info-block">
            <p class="name"><?php echo $_SESSION['monster']->getName(); ?></p><br>
            <div class="enemy-hp">
                <label for="fuel">HP:</label>
                <meter class="meter" min="0" max="<?php echo $_SESSION['monster']->getMaxHp(); ?>" low="70" high="140" optimum="180" value="<?php echo $_SESSION['monster']->gethp(); ?>"></meter>
            </div>
          </div>
          <div class="enemy-img">
              <img src="<?php echo $_SESSION['monster']->getImg(); ?>">
          </div>
      </section>
      

  <!-- 主人公の情報 -->
      <section class="hero">
          <div class="hero-img">
              <img src="img/syujinnkou.png">
          </div>

          <div class="hero-info-block">

            <div class="hero-hp">
                <label for="fuel">HP:</label>
                <meter class="meter" min="0" max="<?php echo $_SESSION['human']->getMaxHp(); ?>" low="100" high="300" optimum="400" value="<?php echo $_SESSION['human']->getHp(); ?>"></meter>
            </div>

            <div class="form-container">
                <form method="post">
                    <input type="submit" name="attack" value="▶︎なぐる">
                    <input type="submit" name="escape" value="▶︎にげる">
                    <input type="submit" name="start" value="▶︎ゲームリスタート">
                </form>
            </div>
            <!-- 倒した数カウント -->
            <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>

          </div>

      </section>

   </div>

<!-- 履歴表示画面 -->
<!-- 必ず最下部までスクロール -->
   <div class="history-display" id="js-scroll"> 
       <p class="history-count"><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
   </div>

<?php } ?>

<script>
    //自動で新しいコメントが来たら下へスクロール
    //自動でページがロードされたら一番下までスクロール
    var obj = document.getElementById('js-scroll');
    obj.scrollTop = obj.scrollHeight;
</script>
  </body>
</html>