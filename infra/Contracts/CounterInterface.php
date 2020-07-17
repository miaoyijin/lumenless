<?php

namespace infra\contracts;

interface CounterInterface
{
    /**
     * 设置定时清理计时器的时间间隔
     * @param int $interval
     * @return CounterInterface
     */
    public function setInterval(int $interval = 0): CounterInterface;

    /**
     * @param string $name
     * @return CounterInterface
     */
    public function setName(string $name): CounterInterface;

    /**
     * @return int
     */
    public function getValue(): int;

    /**
     * 累加计数器
     * @param int $step
     * @return int
     */
    public function increase(int $step = 1): int;

    /**
     * 清理计时器
     */
    public function clear(): void;
}
