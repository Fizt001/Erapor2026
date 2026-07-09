<?php
echo \DB::select('SHOW COLUMNS FROM siswa LIKE "status_siswa"')[0]->Type;
