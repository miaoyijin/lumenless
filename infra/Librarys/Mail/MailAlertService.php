<?php

namespace infra\Librarys\Mail;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use infra\contracts\CounterInterface;
use infra\contracts\MailAlertInterface;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class MailAlertService implements MailAlertInterface
{
    private $level;
    private $interval;
    private $maxTimes;
    private $emails;

    private $problem = "";
    private $title = "";
    private $content = "";
    private $attach = "";
    /**
     * @var Exception
     */
    private $exception;

    /**
     * @var CounterInterface
     */
    private $counter;

    private static $txtForLevel = [
        self::LEVEL_NORMAL => '一般',
        self::LEVEL_WARNING => '警告',
        self::LEVEL_ERROR => '错误',
        self::LEVEL_FATAL => '致命错误'
    ];

    /**
     * MailAlertService constructor.
     * @param CounterInterface $counter
     */
    public function __construct(CounterInterface $counter)
    {
        $this->counter = $counter;

        $times = config("alert.mail.maxTimes");
        $interval = config("alert.mail.interval");
        $targets = config("alert.mail.targets");

        $this->setLevel();
        $this->setAlertRatio($times, $interval);
        $this->setEmails($targets);
    }

    /**
     * 设置告警级别
     * @param int $level
     * @return MailAlertInterface
     */
    public function setLevel(int $level = self::LEVEL_WARNING): MailAlertInterface
    {
        assert(isset($level, self::$txtForLevel));
        $this->level = $level;
        return $this;
    }

    /**
     * 限定时间内同一问题避免多次告警
     * @param int $times
     * @param int $interval
     * @return MailAlertInterface
     */
    public function setAlertRatio(
        int $times = self::DEFAULT_TIMES,
        int $interval = self::DEFAULT_INTERVAL
    ): MailAlertInterface {
        $this->maxTimes = $times;
        $this->interval = $interval;
        $this->counter->setInterval($interval);

        return $this;
    }

    /**
     * 告警目标邮箱
     * @param array $emails
     * @return MailAlertInterface
     */
    public function setEmails(array $emails): MailAlertInterface
    {
        $this->emails = array_filter($emails, function ($email) {
            return $email;
        });
        $this->emails = array_unique($this->emails);
        return $this;
    }

    /**
     * 添加告警目标邮箱
     * @param array $emails
     * @return MailAlertInterface
     */
    public function addEmails(array $emails): MailAlertInterface
    {
        $this->emails = array_merge($this->emails, $emails);
        $this->emails = array_unique($this->emails);
        return $this;
    }

    /**
     * @param string $problem
     * @return MailAlertInterface
     */
    public function setProblem(string $problem): MailAlertInterface
    {
        $this->problem = $problem;
        return $this;
    }

    /**
     * @param string $title [警告][问题类型]title
     * @return MailAlertInterface
     */
    public function setTitle(string $title): MailAlertInterface
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $content
     * @return MailAlertInterface
     */
    public function setContent(string $content): MailAlertInterface
    {
        $this->exception = null;
        $this->content = $content;
        return $this;
    }

    /**
     * @param Exception $exception
     * @return MailAlertInterface
     */
    public function setException(Exception $exception): MailAlertInterface
    {
        $this->exception = $exception;
        $this->content = '';
        return $this;
    }

    public function withAttach(string $path): MailAlertInterface
    {
        $this->attach = $path;
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function getMaxTimes(): int
    {
        return $this->maxTimes;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getProblem(): string
    {
        return $this->problem;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * 真正实施告警返回true，因固定时间内超过限定次数取消告警返回false，其它抛出异常
     * @return bool
     * @throws
     */
    public function alert(): bool
    {
        $logger = Log::stack(['mail-alert']);
        if ($this->exception) {
            $render = new HtmlErrorRenderer(true);
            $exception = FlattenException::create($this->exception);
            $mail = new ExceptionOccur();
            $mail->title = $this->buildTitle();
            $mail->styles = $render->getStylesheet();
            $mail->content = $exception->getAsString();
//            $mail->content = $render->getBody($exception);
        } else {
            $mail = new AlertMail();
            $mail->title = $this->buildTitle();
            $mail->content = $this->buildContent();
        }

        if (!$this->emails) {
            $logger->error("Alert Without Targets", [
                'problem' => $this->problem,
                'title' => $mail->title,
                'content' => $mail->content,
            ]);
            throw new Exception("Alert Without Targets");
        }
        $this->attach && $mail->attach($this->attach, [
            'as' => 'details.txt'
        ]);

        foreach ($this->emails as $email) {
            $this->counter->setName($this->buildCounterName($email));

            if ($this->shouldAlert($this->counter->increase())) {
                try {
                    $start = microtime(true);
                    Mail::to([$email])->send(clone $mail);
                    $timeUsage = microtime(true) - $start;
                    $logger->info(
                        "Alert By Mail to {$email} Success; Time Usage:{$timeUsage}; problem:{$this->problem}",
                        [
                            'problem' => $this->problem,
                            'title' => $mail->title,
                            'content' => $mail->content,
                            'attach' => $this->attach ? substr(file_get_contents($this->attach), 0, 1024) : 'null'
                        ]);
                } catch (Exception $exception) {
                    $logger->error("Exception When Send Alert Email; Error:", [
                        'problem' => $this->problem,
                        'title' => $mail->title,
                        'content' => $this->exception ? $this->exception : $this->content,
                        'exception' => $exception
                    ]);
                }
            } else {
                $logger->info("Ignored; Alert By Mail To {$email} Too Many Times; problem:{$this->problem}", [
                    'problem' => $this->problem,
                    'title' => $mail->title,
                    'content' => $this->exception ? $this->exception : $this->content,
                ]);
            }
        }
        return true;
    }

    private function shouldAlert(int $count)
    {
        return $count <= $this->maxTimes;
    }

    private function buildTitle()
    {
        $level = self::$txtForLevel[$this->level];
        return sprintf("[%s][%s][%s]%s", config('app.env'), $level, $this->problem, $this->title);
    }

    private function buildContent()
    {
        return $this->content;
    }

    /**
     * 生成控制告警邮件发送频次使用的计数器key
     * @param $email
     * @return string
     */
    private function buildCounterName(string $email)
    {
        return md5($email . $this->problem);
    }

}
