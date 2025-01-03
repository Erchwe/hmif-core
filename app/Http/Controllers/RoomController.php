<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{

    public function getAllRoom()
    {
        $ruangan = Ruangan::all();

        return $ruangan;
    }

    public function showRoomHima()
    {
        $ruangan = $this->getAllRoom();

        $availableRooms = $ruangan->filter(function ($room) {
            return $room->ketersediaan == 1;
        });

        return view('hmif.ketersediaanRuangan',[
            'availableRooms' => $availableRooms
        ]);
    }

    //    Not Tested yet
    public function showRoomStaff()
    {
        return view('staff.ketersediaanRuangan',[
            'ruangan' => $this->$ruangan
        ]);
    }

    public function showRoomKalab()
    {
        $ruangan = $this->getAllRoom();
        return view('kalab.room-view', [
            'ruangan'=> $ruangan
        ]);
    }


//  Fetch all jadwal ruangan data from DB
//  Used in :
//  HmifController jadwalRuangan()
    public function getJadwalRuangan()
    {
        $jadwalRuangan = DB::table('jadwal_ruangan')
            ->join('ruangan', 'jadwal_ruangan.room_id', '=', 'ruangan.room_id')
            ->select(
                'jadwal_ruangan.id',
                'jadwal_ruangan.room_id',
                'jadwal_ruangan.tanggal',
                'jadwal_ruangan.jam_mulai',
                'jadwal_ruangan.jam_selesai',
                'jadwal_ruangan.keterangan',
                'ruangan.nama_ruangan'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'room_id' => $item->room_id,
                    'title' => $item->keterangan,
                    'start' => $item->tanggal . 'T' . $item->jam_mulai,
                    'end' => $item->tanggal . 'T' . $item->jam_selesai,
                    'description' => $item->nama_ruangan,
                ];
            });

        return $jadwalRuangan;

//        return dd($jadwalRuangan);
    }

//    Finding room information
    public function getRoomInfo($id)
    {

        $ruangan = Ruangan::with('fasilitas')->findOrFail($id);


        return response()->json([
            'nama_ruangan' => $ruangan->nama_ruangan,
            'kapasitas' => $ruangan->kapasitas,
            'fasilitas' => $ruangan->fasilitas->map(function ($fasilitas) {
                return [
                    'nama_barang' => $fasilitas->nama_barang,
                    'kondisi' => $fasilitas->kondisi,
                ];
            }),
        ]);
    }


}
