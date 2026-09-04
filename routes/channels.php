<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('vehicles', fn () => true);
