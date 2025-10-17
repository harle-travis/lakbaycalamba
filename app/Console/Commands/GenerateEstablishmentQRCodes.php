<?php

namespace App\Console\Commands;

use App\Models\Establishment;
use Illuminate\Console\Command;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateEstablishmentQRCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'establishments:generate-qr-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate QR codes for all establishments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $establishments = Establishment::all();
        
        $this->info("Generating QR codes for {$establishments->count()} establishments...");
        
        foreach ($establishments as $establishment) {
            // Generate QR code URL that points to the stamp scan page (GET route)
            $qrUrl = url("/stamp/process/{$establishment->id}");
            
            // Generate QR code as SVG string
            $qrCode = QrCode::size(200)->generate($qrUrl);
            
            // Update establishment with QR code
            $establishment->update(['qr_code' => $qrCode]);
            
            $this->line("Generated QR code for: {$establishment->establishment_name}");
        }
        
        $this->info("QR code generation completed!");
    }
}
