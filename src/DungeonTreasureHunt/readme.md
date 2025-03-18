Dungeon Treasure Hunt Kata
Problem Statement
You are developing a dungeon treasure hunt game. The player moves through a 2D grid where:

"P" represents the player's starting position.
"T" represents a treasure that the player must collect.
"#" represents a wall (impassable).
"." represents an empty space where the player can move.
Write a function canReachTreasure() that returns True if the player can reach at least one treasure ("T") from their
starting position ("P") using valid moves (up, down, left, right). Otherwise, return False.

```text
  P  T
  #  .
```

```php
 $grid = [
   [ "P", "T" ],
   [ "#", "." ],
 ];
```

```text
  #  #  #
  #  P  #
  #  .  #
  #  .  #
  #  #  #
```

```text
   # #
   . .
```

