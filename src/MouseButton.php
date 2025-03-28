<?php

namespace Whisp\Mouse;

enum MouseButton: int
{
    case LEFT = 0;
    case MIDDLE = 1;
    case RIGHT = 2;
    case RELEASED = 3;
    case RELEASED_LEFT = 4;
    case RELEASED_RIGHT = 5;
    case RELEASED_MIDDLE = 6;
    case WHEEL_UP = 64;
    case WHEEL_DOWN = 65;
}
