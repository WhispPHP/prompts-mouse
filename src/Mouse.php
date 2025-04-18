<?php

namespace Whisp\Mouse;

class Mouse
{
    public ?MouseButton $lastButtonDown = null;

    public int $x = 0;

    public int $y = 0;

    /**
     * Parse a mouse event sequence into its components
     *
     * @param  string  $sequence  The full mouse event sequence
     */
    public function parseEvent(string $sequence): ?MouseEvent
    {
        // Standard format is: ESC [ M Cb Cx Cy
        // where Cb = button state + 32
        //       Cx = x coordinate + 32
        //       Cy = y coordinate + 32
        if (strlen($sequence) < 6) {
            return null;
        }

        // Not a mouse event, we might have been passed something else, ignore
        if (false === preg_match('/^\\033\[M$/', $sequence, $matches)) {
            return null;
        }

        $button = ord($sequence[3]) - 32;
        $this->x = ord($sequence[4]) - 32;
        $this->y = ord($sequence[5]) - 32;

        // Try to get the event from either MouseButton or MouseMotion enum
        $event = MouseButton::tryFrom($button) ?? MouseMotion::tryFrom($button) ?? 'unknown';
        if ($event === MouseButton::LEFT || $event === MouseButton::RIGHT || $event === MouseButton::MIDDLE) {
            $this->lastButtonDown = $event;
        } elseif ($event === MouseButton::RELEASED) { // Helpful shortcut for devs - don't just forward ansi events directly when we can make it easier for devs
            $releasedButton = match($this->lastButtonDown) {
                MouseButton::LEFT => MouseButton::RELEASED_LEFT,
                MouseButton::RIGHT => MouseButton::RELEASED_RIGHT,
                MouseButton::MIDDLE => MouseButton::RELEASED_MIDDLE,
                default => MouseButton::RELEASED,
            };

            return new MouseEvent($releasedButton, $this->x, $this->y);
        }

        return new MouseEvent($event, $this->x, $this->y);
    }

    /**
     * Enable mouse tracking (any-event mode)
     */
    public function enable(): string
    {
        return "\033[?1003h";
    }

    public function enableBasic(): string
    {
        return "\033[?1000h";
    }

    public function enableBasicExtra(): string
    {
        return "\033[?1001h";
    }

    /**
     * Disable mouse tracking
     */
    public function disable(): string
    {
        return "\033[?1003l\033[?1000l\033[?1000l";
    }
}
