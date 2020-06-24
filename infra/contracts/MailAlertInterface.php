<?php

namespace infra\contracts;

use Exception;

interface MailAlertInterface
{
    public const LEVEL_NORMAL = 1;
    public const LEVEL_WARNING = 2;
    public const LEVEL_ERROR = 4;
    public const LEVEL_FATAL = 8;

    public const DEFAULT_INTERVAL = 3600;
    public const DEFAULT_TIMES = 1;

    /**
     * 设置告警级别
     * @param int $level
     * @return MailAlertInterface
     */
    public function setLevel(int $level = self::LEVEL_WARNING): MailAlertInterface;

    /**
     * 限定时间内同一问题避免多次告警
     * @param int $times
     * @param int $interval
     * @return MailAlertInterface
     */
    public function setAlertRatio(
        int $times = self::DEFAULT_TIMES,
        int $interval = self::DEFAULT_INTERVAL
    ): MailAlertInterface;

    /**
     * 告警目标邮箱
     * @param array $emails
     * @return MailAlertInterface
     */
    public function setEmails(array $emails): MailAlertInterface;

    /**
     * 添加告警目标邮箱
     * @param array $emails
     * @return MailAlertInterface
     */
    public function addEmails(array $emails): MailAlertInterface;

    /**
     * @param string $problem
     * @return MailAlertInterface
     */
    public function setProblem(string $problem): MailAlertInterface;

    /**
     * 设置邮件标题主要内容
     * 邮件标题：[{告警级别}][{问题类型}]title
     * @param string $title
     * @return MailAlertInterface
     */
    public function setTitle(string $title): MailAlertInterface;

    /**
     * @param string $content
     * @return MailAlertInterface
     */
    public function setContent(string $content): MailAlertInterface;

    /**
     * @param Exception $exception
     * @return MailAlertInterface
     */
    public function setException(Exception $exception): MailAlertInterface;

    public function withAttach(string $path): MailAlertInterface;

    public function getLevel(): int;

    public function getInterval(): int;

    public function getMaxTimes(): int;

    public function getEmails(): array;

    public function getProblem(): string;

    public function getTitle(): string;

    public function getContent(): string;

    /**
     *
     * 真正实施告警返回true，因固定时间内超过限定次数取消告警返回false，其它抛出异常
     * @return bool
     * @throws
     */
    public function alert(): bool;
}
