
import javax.swing.*;
import java.awt.*;
import java.awt.event.*;
import java.io.File;
import java.io.IOException;
import javax.imageio.ImageIO;

//BrickBreakerGame クラス: メインのクラスで、JPanel を継承しています。ゲームの描画や処理を行います。
public class BrickBreakerGame extends JPanel implements ActionListener, MouseMotionListener {

    //クラス変数: ゲームのウィンドウの幅と高さ、パドルの幅と高さ、ボールの直径、ブロックの幅と高さ、およびブロックの数を定義しています。
    private static final int WIDTH = 800;
    private static final int HEIGHT = 600;
    private static final int PADDLE_WIDTH = 100;
    private static final int PADDLE_HEIGHT = 20;
    private static final int BALL_DIAMETER = 20;
    private static final int BLOCK_WIDTH = 70;
    private static final int BLOCK_HEIGHT = 20;
    private static final int NUM_BLOCKS_X = 9;
    private static final int NUM_BLOCKS_Y = 10;

    //インスタンス変数: パドルのX座標、ボールのX座標とY座標、ボールの速度、ゲームの状態などの変数を定義しています。
    private int paddleX = WIDTH / 2 - PADDLE_WIDTH / 2;
    private int ballX = WIDTH / 2 - BALL_DIAMETER / 2;
    private int ballY = HEIGHT / 2 - BALL_DIAMETER / 2;
    private int ballSpeedX = 2;
    private int ballSpeedY = 2;
    private boolean isPlaying = true;
    private boolean[][] blockExists;
    private Image blockImage;

    //コンストラクタ: ゲームの初期化を行います。タイマーを設定し、マウスの移動を監視します。
    // また、ブロックの存在を管理する2次元配列を初期化し、ブロックの画像を読み込みます。
    public BrickBreakerGame() {
        Timer timer = new Timer(5, this);
        timer.start();
        addMouseMotionListener(this);
        blockExists = new boolean[NUM_BLOCKS_Y][NUM_BLOCKS_X];
        for (int i = 0; i < NUM_BLOCKS_Y; i++) {
            for (int j = 0; j < NUM_BLOCKS_X; j++) {
                blockExists[i][j] = true;
            }
        }
        try {
            blockImage = ImageIO.read(new File("images/kame01.png")); // 画像ファイルのパスを指定して読み込む
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    //paintComponent メソッド: ゲーム画面を描画します。背景、パドル、ボール、ブロックを描画します。
    @Override
    protected void paintComponent(Graphics g) {
        super.paintComponent(g);
        drawBackground(g);
        drawPaddle(g);
        drawBall(g);
        drawBlocks(g);
    }

    //描画メソッド: drawBackground、drawPaddle、drawBall、drawBlocks メソッドは、それぞれ背景、パドル、ボール、ブロックを描画します。
    private void drawBackground(Graphics g) {
        g.setColor(Color.WHITE);
        g.fillRect(0, 0, WIDTH, HEIGHT);
    }

    private void drawPaddle(Graphics g) {
        g.setColor(Color.BLUE);
        g.fillRect(paddleX, HEIGHT - PADDLE_HEIGHT - 30, PADDLE_WIDTH, PADDLE_HEIGHT);
    }

    private void drawBall(Graphics g) {
        g.setColor(Color.gray);
        g.fillOval(ballX, ballY, BALL_DIAMETER, BALL_DIAMETER);
    }

    private void drawBlocks(Graphics g) {
        int blockX = 20;
        int blockY = 20;
        for (int i = 0; i < NUM_BLOCKS_Y; i++) {
            for (int j = 0; j < NUM_BLOCKS_X; j++) {
                if (blockExists[i][j]) {
                    g.drawImage(blockImage, blockX, blockY, BLOCK_WIDTH, BLOCK_HEIGHT, this);
                }
                blockX += BLOCK_WIDTH + 10;
            }
            blockX = 20;
            blockY += BLOCK_HEIGHT + 10;
        }
    }

    //actionPerformed メソッド: ゲームのメインループです。ボールの移動や衝突の処理を行います。
    @Override
    public void actionPerformed(ActionEvent e) {
        if (!isPlaying) return;

        ballX += ballSpeedX;
        ballY += ballSpeedY;

        if (ballX <= 0 || ballX >= WIDTH - BALL_DIAMETER) {
            ballSpeedX *= -1;
        }
        if (ballY <= 0) {
            ballSpeedY *= -1;
        }
        if (ballY >= HEIGHT - BALL_DIAMETER - PADDLE_HEIGHT - 30) {
            if (ballX + BALL_DIAMETER >= paddleX && ballX <= paddleX + PADDLE_WIDTH) {
                ballSpeedY *= -1;
            } else {
                // パドルを通り抜けた場合はゲームオーバー
                isPlaying = false;
            }
        }

        checkBlockCollision();

        repaint();
    }

    //checkBlockCollision メソッド: ボールがブロックに衝突したかどうかをチェックし、衝突した場合はブロックを削除します。
    private void checkBlockCollision() {
        int blockX = 20;
        int blockY = 20;
        for (int i = 0; i < NUM_BLOCKS_Y; i++) {
            for (int j = 0; j < NUM_BLOCKS_X; j++) {
                if (blockExists[i][j]) {
                    Rectangle blockRect = new Rectangle(blockX, blockY, BLOCK_WIDTH, BLOCK_HEIGHT);
                    Rectangle ballRect = new Rectangle(ballX, ballY, BALL_DIAMETER, BALL_DIAMETER);
                    if (blockRect.intersects(ballRect)) {
                        blockExists[i][j] = false;
                        ballSpeedY *= -1;
                        ballSpeedX *= 1.1; // 移動速度を増加
                    }
                }
                blockX += BLOCK_WIDTH + 10;
            }
            blockX = 20;
            blockY += BLOCK_HEIGHT + 10;
        }
    }

    //mouseMoved メソッド: マウスの移動を検出し、パドルの位置を更新します。
    @Override
    public void mouseMoved(MouseEvent e) {
        paddleX = e.getX() - PADDLE_WIDTH / 2;
        if (paddleX < 0) paddleX = 0;
        if (paddleX > WIDTH - PADDLE_WIDTH) paddleX = WIDTH - PADDLE_WIDTH;
    }

    @Override
    public void mouseDragged(MouseEvent e) {}

    //main メソッド: ゲームを起動するためのメインメソッドです。
    // JFrame を作成し、BrickBreakerGame クラスのインスタンスを追加します。
    public static void main(String[] args) {
        JFrame frame = new JFrame("Brick Breaker Game");
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        frame.setSize(WIDTH, HEIGHT);
        frame.setResizable(false);
        frame.add(new BrickBreakerGame());
        frame.setVisible(true);
    }
}


