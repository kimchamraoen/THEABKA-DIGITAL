<?php

namespace App\Imports;

use App\Models\Guest;
use Maatwebsite\Excel\Concerns\ToModel;

class GuestsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Guest([
            'guest_name' => $row[1], 
            'phone'      => $row[2],
            'group'      => $row[3],
            'Greeting'   => $row[7] ?? 'N/A',
            'statue'     => $row[4],
            'gift_money' => $row[5] ?? 'N/A',
            'gift'       => $row[6] ?? 'N/A',
            'user_id'    => auth()->id(),
        ]);
    }
}
