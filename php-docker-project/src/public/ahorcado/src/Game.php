<?php
declare(strict_types=1);


final class Game {
    private array $usedLetters;
    private string $word;
    private int $maxAttempts;
    private int $attemptsLeft;

    public function __construct(string $word, int $maxAttempts = 6, ?array $state = null){

    }

    public function guessLetter(string $letter): void {

    }

    public function getMaskedWord(): string {

    }

    public function getAttemptsLeft(): int {

    }

    public function getUsedLetters(): array {

    }

    public function isWon(): bool {

    }

    public function isLost(): bool {

    }

    public function getWord(): string {

    }

    public function toState(): array {
        
    }






}
?>