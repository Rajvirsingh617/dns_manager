<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Zone;
use App\Models\ZoneRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;



class ZoneController extends Controller
{
    protected $dns_url;

    public function __construct()
    {
        // Initialize the DNS URL from environment configuration
        $this->dns_url = env('DNS_APP_URL');
    }

    public function index()
    {
        if (auth()->user()->role === 'admin') {
            $zones = Zone::with('user')->paginate(10); // Corrected the typo here
        } else {
            $zones = Zone::where('owner', auth()->id())->paginate(10);
        }
        return view('zones.index', compact('zones'));
    }


    public function show($id)
    {
        $zone = Zone::findOrFail($id); // Ensure the Zone model is imported at the top
        return view('zones.show', compact('zone')); // Create the `zones/show.blade.php` file
    }


    public function store(Request $request)
    {
        // Validate fields specific to the zone
        $request->validate(
            [
                'name' => ['required', 'unique:zones,name', 'regex:/^(?!-)[a-zA-Z0-9-]{1,63}(?<!-)\.[a-zA-Z]{2,}$/'],
                'refresh' => 'required|integer',
                'retry' => 'required|integer',
                'expire' => 'required|integer',
                'ttl' => 'required|integer',
                'pri_dns' => 'required|string',
                'sec_dns' => 'required|string',
                'records' => 'nullable|array',
                'records.*.host' => 'required|string',
                'records.*.type' => 'required|string',
                'records.*.destination' => 'required|string',
                'user_id' => 'nullable|exists:dns_users,id',
            ],
            [
                'name.required' => 'The zone name is required.',
                'name.regex' => 'The domain name must be valid (e.g., example.com, domain.store).',
                'name.unique' => 'The zone name must be unique.',
            ]
        );

        // Check if the zone already exists
        if (Zone::where('name', $request->name)->exists()) {
            return back()->with('error', 'The zone already exists in the database.');
        }

        // Create the zone
        $zone = new Zone();
        $zone->uuid = Str::uuid()->toString();
        $zone->name = $request->name;
        $zone->refresh = $request->refresh;
        $zone->retry = $request->retry;
        $zone->expire = $request->expire;
        $zone->ttl = $request->ttl;
        $zone->pri_dns = $request->pri_dns;
        $zone->sec_dns = $request->sec_dns;
        $zone->owner = auth()->user()->isAdmin() && $request->user_id ? $request->user_id : Auth::id();
        $zone->save();

        // Prepare zone file
        $username = auth()->user()->username;
        $directory = "/var/www/html/storage/app/coredns/zones/";
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Prepare zone file
        $filename = $directory . "/" . $request->name . ".db";
        $serial = date('Ymd') . '01';

        $zoneContent = "\$ORIGIN {$zone->name}.\n";
        $zoneContent .= "\$TTL {$zone->ttl} ; Default TTL\n\n";

        // SOA Record
        $zoneContent .= "; SOA Record\n";
        $zoneContent .= "@   IN  SOA {$zone->pri_dns}. admin.{$zone->name}. (\n";
        $zoneContent .= sprintf("        %-10d ; Serial (YYYYMMDDNN)\n", $serial);
        $zoneContent .= sprintf("        %-10d ; Refresh (%d seconds)\n", $zone->refresh, $zone->refresh);
        $zoneContent .= sprintf("        %-10d ; Retry (%d seconds)\n", $zone->retry, $zone->retry);
        $zoneContent .= sprintf("        %-10d ; Expire (%d seconds)\n", $zone->expire, $zone->expire);
        $zoneContent .= sprintf("        %-10d ; Minimum TTL (%d seconds)\n", $zone->ttl, $zone->ttl);
        $zoneContent .= ")\n\n";
        // NS Records
        $zoneContent .= "; NS Records\n";
        $zoneContent .= "@   IN  NS  {$zone->pri_dns}.\n";
        $zoneContent .= "@   IN  NS  {$zone->sec_dns}.\n";

        // Ensure $records is always an array
        $records = $request->get('records', []);



        // Define all record types
        $recordTypes = ["A", "A6", "AAAA", "CNAME", "DNAME", "DS", "LOC", "MX", "NAPTR", "NS", "PTR", "RP", "SRV", "SSHFP", "TXT", "WKS"];

        $recordData = [];

        // Organize records into sections
        foreach ($records as $record) {
            $type = strtoupper($record['type']);
            if (!in_array($type, $recordTypes)) continue;

            $priority = ($type === 'MX' || $type === 'SRV') && isset($record['priority']) ? "{$record['priority']} " : '';
            $recordData[$type][] = sprintf("%-8s IN  %-5s %s%s", $record['host'], $type, $priority, $record['destination']);
        }


        // Append record sections dynamically
        foreach ($recordTypes as $type) {
            $zoneContent .= "\n; {$type} Records\n";
            if (!empty($recordData[$type])) {
                $zoneContent .= implode("\n", $recordData[$type]) . "\n";
            } else {
                $zoneContent .= "\n"; // Just an empty line if no records exist for the type
            }
        }
            // Save the .db file
            file_put_contents($filename, $zoneContent);

                $configFilename = $directory . "/" . $request->name . ".conf";
                $configContent = <<<EOF
                {$zone->name} {
                    file /etc/coredns/zones/{$zone->name}.db
                    log
                }
                EOF;
                // Save the formatted .conf file
            file_put_contents($configFilename, $configContent);


            // Save records to the database
            foreach ($records as $record) {
                $zone->records()->create([
                    'host' => $record['host'],
                    'type' => $record['type'],
                    'destination' => $record['destination'],
                    'priority' => $record['priority'] ?? null,
                ]);
            }

            $response = Http::get($this->dns_url.'/reload-coredns');

        return redirect()->route('zones.index')->with('success', 'Zone and records added successfully!');
    }



    public function create()
{
    // Get all users grouped by role
    $admins = User::where('role', 'admin')->get(); // Get all admin users
    $users = User::where('role', '!=', 'admin')->get(); // Get all non-admin users
    $zones = Zone::all();
    return view('zones.newzone', compact('users', 'admins','zones'));
}



    public function destroy($id)
{
    // Find the zone by ID
    $zone = Zone::findOrFail($id);

    // Define the storage path for the zone files
    $directory = "/var/www/html/storage/app/coredns/zones/";
    $zoneFilename = $directory . "/" . $zone->name . ".db";
    $configFilename = $directory . "/" . $zone->name . ".conf";

    // Delete associated records
    $zone->records()->delete();

    // Delete the zone from the database
    $zone->delete();

    // Remove zone files if they exist
    if (file_exists($zoneFilename)) {
        unlink($zoneFilename);
    }
    if (file_exists($configFilename)) {
        unlink($configFilename);
    }

    // Reload CoreDNS to apply changes
    Http::get($this->dns_url . '/reload-coredns');

    return redirect()->route('zones.index')->with('success', 'Zone and records deleted successfully!');
}



    public function edit($id)
    {
        // Find the zone by ID and pass it to the edit view
        $zone = Zone::with('records')->findOrFail($id);
        $users = User::all();
        return view('zones.editzone', compact('zone', 'users'));
    }


    public function update(Request $request, $id)
    {
        // Find the Zone by its ID
        $zone = Zone::findOrFail($id);

        // Validate the incoming request
        $request->validate([
            'name' => 'required|string',
            'refresh' => 'required|integer',
            'retry' => 'required|integer',
            'expire' => 'required|integer',
            'ttl' => 'required|integer',
            'pri_dns' => 'required|string',
            'sec_dns' => 'required|string',
            'www' => 'nullable',
            'mail' => 'nullable',
            'ftp' => 'nullable',
            'owner' => 'required|exists:dns_users,id',
        ]);

        // Update the Zone with the new data
        $zone->update($request->only([
            'name',
            'refresh',
            'retry',
            'expire',
            'ttl',
            'pri_dns',
            'sec_dns',
            'www',
            'mail',
            'ftp',
            'owner'
        ]));

        // Get the username and path to the user's zone directory
        $username = auth()->user()->username;
        $directory = "/var/www/html/storage/app/coredns/zones/" ;
        $filename = $directory . "/" . $zone->name . ".zone";

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        // Regenerate the zone file with updated information
        $serial = date('Ymd') . '01'; // Updated serial based on date
        $zoneContent = "
\$TTL 3600
@       IN      SOA     ns1." . $zone->name . ". admin." . $zone->name . ". (
                $serial              ; Serial
                {$zone->refresh}     ; Refresh
                {$zone->retry}       ; Retry
                {$zone->expire}      ; Expire
                {$zone->ttl}         ) ; Minimum TTL

IN      NS      ns1." . $zone->name . ".
IN      NS      ns2." . $zone->name . ".
";

        // Overwrite the existing zone file with updated content
        file_put_contents($filename, $zoneContent);

        // Handle any new or updated records
        foreach ($zone->records as $record) {
            $recordContent = "{$record->host}    IN    {$record->type}    {$record->destination}\n";
            if ($record->type === 'MX') {
                $recordContent = "{$record->host}    IN    MX    {$record->priority} {$record->destination}\n";
            }
            file_put_contents($filename, $recordContent, FILE_APPEND);
        }

        // Redirect with a success message
        return redirect()->route('zones.index')->with('success', 'Zone updated successfully!');
    }




    public function updateRecords(Request $request, $zoneId)
{
    // Get the zone and validate the user permission
    $zone = Zone::findOrFail($zoneId);

    // Handle deletion of records
    if ($request->has('delete')) {
        foreach ($request->delete as $recordId => $value) {
            if ($value) {
                $zone->records()->where('id', $recordId)->delete();
            }
        }
    }

    // Handle updates of records
    foreach ($request->record_id as $key => $recordId) {
        $record = $zone->records()->findOrFail($recordId);
        $record->update([
            'host' => $request->host[$key],
            'type' => $request->type[$key],
            'destination' => $request->destination[$key],
        ]);
    }

    // Handle new record creation
    if ($request->has('newhost')) {
        $newRecord = $zone->records()->create([
            'host' => $request->newhost,
            'type' => $request->newtype,
            'destination' => $request->newdestination,
        ]);

        // Now update the zone file at the specified location
        $username = auth()->user()->username;
        $directory = "/var/www/html/storage/app/coredns/zones/" /* . $username */;
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filename = $directory . "/" . $zone->name . ".zone";

        // Get the existing content of the zone file
        $zoneContent = file_get_contents($filename);

        // Ensure all sections are created
        $zoneContent = $this->ensureSectionExists($zoneContent, 'A Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'MX Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'CNAME Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'AAAA Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'DNAME Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'DS Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'LOC Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'MX Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'NAPTR Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'NS Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'PTR Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'RP Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'SRV Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'SSHFP Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'TXT Records');
        $zoneContent = $this->ensureSectionExists($zoneContent, 'WKS Records');

        // Add the new record to the appropriate section based on its type
        $recordLine = "{$newRecord->host}    IN    {$newRecord->type}    {$newRecord->destination}\n";
        $zoneContent = $this->addRecordToSection($zoneContent, $newRecord->type, $recordLine);

        // Save the updated zone file
        file_put_contents($filename, $zoneContent);
    }

    return redirect()->route('zones.edit', $zoneId)->with('success', 'Records updated successfully!');
}

private function ensureSectionExists($zoneContent, $section)
{
    // Ensure a blank line before the section
    if (strpos($zoneContent, "; {$section}") === false) {
        // Add a blank line before the section
        $zoneContent .= "\n; {$section}\n";
    } else {
        // Add a blank line if not already present
        $zoneContent = preg_replace("/(; {$section})(.*?)(?=\n;|\z)/s", "\n$0", $zoneContent);
    }

    return $zoneContent;
}

private function addRecordToSection($zoneContent, $recordType, $recordLine)
{
    $section = strtoupper($recordType) . " Records";
    if (strpos($zoneContent, "; {$section}") !== false) {
        // Ensure a blank line between records in the section
        $zoneContent = preg_replace_callback("/(; {$section})(.*?)(?=\n;|\z)/s", function ($matches) use ($recordLine) {
            return $matches[0] . "\n" . $recordLine;
        }, $zoneContent);
    } else {
        // If the section doesn't exist, create it and add the record
        $zoneContent .= "\n; {$section}\n" . $recordLine;
    }

    return $zoneContent;
}




    private function getDestinationValidationRule($type)
    {
        switch ($type) {
            case 'A':
                // IPv4 address validation
                return ['required', 'ipv4'];

            case 'AAAA':
                // IPv6 address validation
                return ['required', 'ipv6'];

            case 'A6':
                // A6 record: prefix length and IPv6 address
                // Format: "prefix-length IPv6-address" (e.g., "24 2001:db8::1")
                return [
                    'required',
                    'string',
                    'regex:/^\\d{1,3}\\s([a-fA-F0-9]{1,4}(:[a-fA-F0-9]{0,4}){2,7})$/'
                ];

            case 'CNAME':
            case 'DNAME':
            case 'NS':
            case 'PTR':
                // Domain name validation
                return ['required', 'string', 'regex:/^([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'MX':
                // Mail server priority and domain name validation
                // Example: "10 mail.example.com"
                return ['required', 'string', 'regex:/^\\d+\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'TXT':
                // Free-form text validation
                return ['required', 'string'];

            case 'LOC':
                // Geographic location in the format: "37 23 30.0 N 121 58 21.0 W 10m"
                return ['required', 'regex:/^(\\d{1,2}\\s\\d{1,2}\\s\\d{1,2}\\.[0-9]+\\s[N|S]\\s\\d{1,3}\\s\\d{1,2}\\s\\d{1,2}\\.[0-9]+\\s[W|E]\\s\\d+m)$/'];

            case 'SRV':
                // Service record format: "10 5 5060 sipserver.example.com"
                return ['required', 'regex:/^\\d+\\s\\d+\\s\\d+\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'SSHFP':
                // SSHFP: Algorithm, Fingerprint Type, Fingerprint
                return ['required', 'string', 'regex:/^\\d+\\s\\d+\\s[a-fA-F0-9]{40,64}$/'];

            case 'DS':
                // DNSSEC Delegation Signer: Key Tag, Algorithm, Digest Type, Digest
                return ['required', 'string', 'regex:/^\\d+\\s\\d+\\s\\d+\\s[a-fA-F0-9]+$/'];

            case 'NAPTR':
                // NAPTR: Order, Preference, Flags, Service, Regexp, Replacement
                return ['required', 'string', 'regex:/^\\d+\\s\\d+\\s\\\"[a-zA-Z0-9]+\\\"\\s\\\"[a-zA-Z0-9]+\\\"\\s\\\".*\\\"\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'RP':
                // Responsible Person: email address (in DNS format) and TXT record pointer
                return ['required', 'string', 'regex:/^([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'WKS':
                // Well-Known Services: Protocol, IP, Service Map
                return ['required', 'string']; // Adjust based on specific WKS usage (rarely used)

            default:
                // Default fallback for any unhandled types
                return ['required', 'string'];
        }
    }


    protected function updateZoneFile(Zone $zone)
    {
        $username = auth()->user()->username;
        $directory = "/var/www/html/storage/app/coredns/zones/" . $username;
        $filename = $directory . "/" . $zone->name . ".zone";

        // Check if the directory exists; if not, create it
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true); // Create directory if it doesn't exist
        }

        // Check if the zone file already exists
        if (!file_exists($filename)) {
            // If the file doesn't exist, create a new one
            // You may want to return an error or a success message here
            return "Zone file does not exist for updating. Please create it first.";
        }

        // Prepare the updated serial number and zone content
        $serial = date('Ymd') . '01'; // Serial format YYYYMMDDnn
        $zoneContent = "
        \$TTL 3600
        @       IN      SOA     ns1." . $zone->name . ". admin." . $zone->name . ". (
                            $serial              ; Serial
                            $zone->refresh       ; Refresh
                            $zone->retry         ; Retry
                            $zone->expire        ; Expire
                            $zone->ttl           )      ; Minimum TTL
        ";

        $zoneContent .= "
        IN      NS      ns1." . $zone->name . ".
        IN      NS      ns2." . $zone->name . ".
        ";

        // Add records to the zone content
        foreach ($zone->records as $record) {
            $recordContent = "{$record->host}    IN    {$record->type}    {$record->destination}\n";
            if ($record->type === 'MX') {
                $recordContent = "{$record->host}    IN    MX    {$record->priority} {$record->destination}\n";
            }
            $zoneContent .= $recordContent;
        }

        // Update the existing zone file content (overwrite it)
        file_put_contents($filename, $zoneContent);
         // Call the external API
        $response = Http::get($this->dns_url.'/reload-coredns');

        return "Zone file updated successfully.";
    }




    public function indexApi(Request $request)
    {
        // Fetch the API key from the Authorization header
        $apiKey = $request->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the API key is valid
        if (!$user || $user->api_token !== $apiKey) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        // Fetch zones for the authenticated user
        $zones = Zone::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('owner', $user->id);
        })->get();

        // Return the zones as a JSON response
        return response()->json($zones, 200);
    }


    public function showApi(Request $request, $id)
    {
        // Fetch the API key from the Authorization header
        $apiKey = $request->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the API key is valid
        if (!$user || $user->api_token !== $apiKey) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        // Fetch the specific zone for the authenticated user
        $zone = Zone::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('owner', $user->id);
        })->where('id', $id)->first();

        if (!$zone) {
            return response()->json(['error' => 'Zone not found or access denied.'], 404);
        }

        // Return the zone as a JSON response
        return response()->json($zone, 200);
    }


    public function storeApi(Request $request)
    {
        // Fetch the API key from the Authorization header
        $apiKey = $request->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the API key is valid
        if (!$user || $user->api_token !== $apiKey) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        // Validate the request
        $validated = $request->validate([
            'name' => 'required|unique:zones,name',
            'refresh' => 'required|integer',
            'retry' => 'required|integer',
            'expire' => 'required|integer',
            'ttl' => 'required|integer',
            'pri_dns' => 'required|string',
            'sec_dns' => 'required|string',
            'www' => 'nullable',
            'mail' => 'nullable',
            'ftp' => 'nullable',
        ]);

        $validated['owner'] = $user->id;
        // Create the zone
        $zone = Zone::create($validated);

        // Get the username of the authenticated user
        $username = $user->username;
        $zoneName = $zone->name;
        $directory = "/var/www/html/storage/app/coredns/zones/" /* . $username */;

        // Ensure the directory exists
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Define the zone file path
        $filename = $directory . "/" . $zoneName . ".db";
        $serial = date('Ymd') . '01';
         $zoneContent =ltrim("
\$TTL {$zone->ttl}
@   IN  SOA     {$zone->pri_dns}. admin.{$zone->name}. (
                    {$serial}           ; Serial
                    {$zone->refresh}           ; Refresh
                    {$zone->retry}            ; Retry
                    {$zone->expire}         ; Expire
                    {$zone->ttl}           ; Minimum TTL
)

; Name Servers (NS)
@       IN      NS      {$zone->pri_dns}.
@       IN      NS      {$zone->sec_dns}.
" ,"\n");



        // Write the zone content to the file
        file_put_contents($filename, $zoneContent);

        // Handle dynamic or random record creation
        $records = $request->get('records', []); // Records provided in the request
        if (empty($records)) {
            // Generate random records if none are provided
            $records = [
                ['host' => '@', 'type' => 'A', 'destination' => $zone->www],
                ['host' => 'ftp', 'type' => 'A', 'destination' => $zone->ftp],
                ['host' => 'mail', 'type' => 'A', 'destination' => $zone->mail],
                ['host' => 'www', 'type' => 'CNAME', 'destination' => '@'],
                ['host' => '@', 'type' => 'MX', 'destination' => 'mail.' . $zone->name, 'priority' => 10],
            ];
        }

        // Loop through the records and save them to the database
        $recordTypes = [];
        foreach ($records as $record) {
            $type = $record['type'];
            $recordTypes[$type][] = $record;
        }

        foreach ($recordTypes as $type => $groupedRecords) {
            $zoneContent .= "\n; {$type} Records\n"; // Heading
            foreach ($groupedRecords as $record) {
                $priority = ($type === 'MX' || $type === 'SRV') && isset($record['priority']) ? "{$record['priority']} " : '';
                $zoneContent .= "{$record['host']}    IN    {$type}    {$priority}{$record['destination']}\n";
            }
        }

        // Save zone file
        file_put_contents($filename, $zoneContent);
          // Create the second file: .conf
          // Create the second file: .conf
$configFilename = $directory . "/" . $request->name . ".conf";
$configContent = "{$zone->name} {
    file /etc/coredns/zones/{$zone->name}.db
    log
}";

// Save .conf file
file_put_contents($configFilename, $configContent);


        // Save records to the database
        foreach ($records as $record) {
            $zone->records()->create([
                'host' => $record['host'],
                'type' => $record['type'],
                'destination' => $record['destination'],
                'priority' => $record['priority'] ?? null,
            ]);
        }

        $response = Http::get($this->dns_url.'/reload-coredns');
        // Return the zone as a JSON response
        return response()->json($zone, 201);
    }

    public function updateApi(Request $request, $id)
    {
        try {
            // Extract API key from Authorization header
            $apiKey = $request->header('Authorization');
            $apiKey = str_replace('Bearer ', '', $apiKey);

            \Log::info('Received API key: ' . $apiKey);

            // Authenticate user by API token
            $user = \App\Models\User::where('api_token', $apiKey)->first();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
            }

            \Log::info('Authenticated User ID: ' . $user->id);

            // Find the zone by ID
            $zone = \App\Models\Zone::find($id);

            if (!$zone) {
                return response()->json(['error' => 'Zone not found'], 404);
            }

            // Check ownership
            if ($zone->owner !== $user->id) {
                return response()->json(['error' => 'You are not authorized to update this zone'], 403);
            }

            // Validate the request
            $validated = $request->validate([
                'name' => 'sometimes|required|unique:zones,name,' . $zone->id . '|regex:/^[a-zA-Z0-9.-]+$/',
                'refresh' => 'sometimes|required|integer',
                'retry' => 'sometimes|required|integer',
                'expire' => 'sometimes|required|integer',
                'ttl' => 'sometimes|required|integer',
                'pri_dns' => 'sometimes|required|string',
                'sec_dns' => 'sometimes|required|string',
                'www' => 'nullable',
                'mail' => 'nullable',
                'ftp' => 'nullable',
            ]);

            \Log::info('Validated Data: ', $validated);

            // Handle old zone file if the name changes
            $oldFilename = "/var/www/html/storage/app/coredns/zones/" . $user->username . "/" . $zone->name . ".zone";
            if (isset($validated['name']) && $validated['name'] !== $zone->name) {
                if (file_exists($oldFilename)) {
                    unlink($oldFilename);
                }
            }

            // Update the zone
            $zone->update($validated);

            // Ensure the directory exists
            $directory = "/var/www/html/storage/app/coredns/zones/" . $user->username;
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate the updated zone file
            $filename = $directory . "/" . $zone->name . ".zone";
            $serial = date('Ymd') . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
            $zoneContent = <<<ZONE
    \$TTL {$zone->ttl}
    @       IN      SOA     {$zone->pri_dns}. admin.{$zone->name}. (
                    {$serial}        ; Serial
                    {$zone->refresh} ; Refresh
                    {$zone->retry}   ; Retry
                    {$zone->expire}  ; Expire
                    {$zone->ttl}     ; Minimum TTL
    )
    @       IN      NS      {$zone->pri_dns}.
    @       IN      NS      {$zone->sec_dns}.
    @       IN      A       {$zone->www}
    ftp     IN      A       {$zone->ftp}
    mail    IN      A       {$zone->mail}
    www     IN      CNAME   @
    @       IN      MX      10 mail.{$zone->name}.
    ZONE;

            if (file_put_contents($filename, $zoneContent) === false) {
                return response()->json(['error' => 'Failed to write zone file'], 500);
            }

            \Log::info('Zone file updated: ' . $filename);

            $response = Http::get($this->dns_url.'/reload-coredns');

            return response()->json($zone, 200);
        } catch (\Exception $e) {
            \Log::error('Update Zone Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    public function destroyApi($id)
    {
        // Extract API key from Authorization header
        $apiKey = request()->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Find the authenticated user by API token
        $user = \App\Models\User::where('api_token', $apiKey)->first();

        // Check if user exists and is authenticated
        if (!$user) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        // Find the zone by ID
        $zone = \App\Models\Zone::find($id);

        // Check if the zone exists
        if (!$zone) {
            return response()->json(['error' => 'Zone not found'], 404);
        }

        // Ensure the authenticated user is the owner of the zone
        if ($zone->owner !== $user->id) {
            return response()->json(['error' => 'You are not authorized to delete this zone'], 403);
        }

        // Prepare the file path for the zone file
        $username = $user->username;
        $directory = "/var/www/html/storage/app/coredns/zones/" . $username;
        $filename = $directory . "/" . $zone->name . ".zone";

        // Delete the zone file if it exists
        if (file_exists($filename)) {
            unlink($filename); // Deletes the zone file from the server
        }

        // Delete the zone from the database
        $zone->delete();

        $response = Http::get($this->dns_url.'/reload-coredns');

        return response()->json(['message' => 'Zone deleted successfully'], 200);
    }

    public function storeRecordApi(Request $request, $uuid)
{
    // API Key Authentication
    $apiKey = $request->header('Authorization');
    $apiKey = str_replace('Bearer ', '', $apiKey);

    // Fetch authenticated user
    $user = Auth::user();

    if (!$user || $user->api_token !== $apiKey) {
        return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
    }

    // Find the zone by UUID
    $zone = \App\Models\Zone::where('uuid', $uuid)->first();

    if (!$zone) {
        return response()->json(['error' => 'Zone not found'], 404);
    }

    // Ensure the user has access to the zone
    if ($zone->owner !== $user->id) {
        return response()->json(['error' => 'Unauthorized to add records for this zone'], 403);
    }

    // Dynamic validation rules for the `destination` field based on record type
    $destinationRule = $this->getDestinationValidationRuleapi($request->type);

    // Validate the request data
    $validated = $request->validate([
        'host' => [
            'required',
            'string',
            'max:255',
            'regex:/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][a-zA-Z0-9\-]*[A-Za-z0-9])$/'
        ],
        'type' => 'required|in:A,AAAA,CNAME,DNAME,DS,LOC,MX,NAPTR,NS,PTR,RP,SRV,SSHFP,TXT,WKS',
        'destination' => $destinationRule,
        'priority' => 'nullable|integer|min:0', // Applicable for MX or SRV records
    ]);

    // Define the path to save the zone record
    $usernameFolder = $user->username; // Assuming a 'username' field exists in the users table
    $zoneName = $zone->name;
    $zoneDirectory = "/var/www/html/storage/app/coredns/zones/{$usernameFolder}";
    $zoneFilePath = "{$zoneDirectory}/{$zoneName}.zone";

    // Ensure the folder exists
    if (!is_dir($zoneDirectory)) {
        mkdir($zoneDirectory, 0755, true);
    }

    // Generate the DNS record entry
    $dnsEntry = "{$validated['host']} IN {$validated['type']} {$validated['destination']}";
    if (!empty($validated['priority']) && $validated['type'] === 'MX') {
        $dnsEntry = "{$validated['host']} IN {$validated['type']} {$validated['priority']} {$validated['destination']}";
    }

    // Check if record already exists
    if (
        $this->recordExistsInZoneFile($zoneFilePath, $dnsEntry)
    ) {
        return response()->json(['error' => 'Duplicate record detected.'], 400);
    }

    try {
        // Append the record to the zone file
        file_put_contents($zoneFilePath, $dnsEntry . PHP_EOL, FILE_APPEND | LOCK_EX);

        // Add the record to the database
        $zone->records()->create([
            'host' => $validated['host'],
            'type' => $validated['type'],
            'destination' => $validated['destination'],
            'priority' => $validated['priority'] ?? null,
        ]);

        $response = Http::get($this->dns_url.'/reload-coredns');
        // Return success response with 201 status code
        return response()->json([
            'message' => 'Record added successfully',
            'record' => $dnsEntry
        ], 201);

    } catch (\Exception $e) {
        // Handle any file-related or database errors
        return response()->json(['error' => 'Failed to add record: ' . $e->getMessage()], 500);
    }
}

    private function recordExistsInZoneFile($zoneFilePath, $dnsEntry)
    {
        // Check if the DNS record already exists in the zone file
        if (file_exists($zoneFilePath)) {
            $fileContents = file_get_contents($zoneFilePath);
            return strpos($fileContents, $dnsEntry) !== false;
        }
        return false;
    }


    private function getDestinationValidationRuleapi($type)
    {
        // Validate destination based on the DNS record type
        switch ($type) {
            case 'A':
                // IPv4 address validation
                return ['required', 'ipv4'];

            case 'AAAA':
                // IPv6 address validation
                return ['required', 'ipv6'];

            case 'A6':
                // A6 record: prefix length and IPv6 address
                // Format: "prefix-length IPv6-address" (e.g., "24 2001:db8::1")
                return [
                    'required',
                    'string',
                    'regex:/^\\d{1,3}\\s([a-fA-F0-9]{1,4}(:[a-fA-F0-9]{0,4}){2,7})$/'
                ];

            case 'CNAME':
            case 'DNAME':
            case 'NS':
            case 'PTR':
                // Domain name validation
                return ['required', 'string', 'regex:/^([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'MX':
                // Mail server priority and domain name validation
                // Example: "10 mail.example.com"
                return ['required', 'string', 'regex:/^\\d+\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'TXT':
                // Free-form text validation
                return ['required', 'string'];

            case 'LOC':
                // Geographic location in the format: "37 23 30.0 N 121 58 21.0 W 10m"
                return ['required', 'regex:/^(\\d{1,2}\\s\\d{1,2}\\s\\d{1,2}\\.[0-9]+\\s[N|S]\\s\\d{1,3}\\s\\d{1,2}\\s\\d{1,2}\\.[0-9]+\\s[W|E]\\s\\d+m)$/'];

            case 'SRV':
                // Service record format: "10 5 5060 sipserver.example.com"
                return ['required', 'regex:/^\\d+\\s\\d+\\s\\d+\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'SSHFP':
                // SSHFP: Algorithm, Fingerprint Type, Fingerprint
                return ['required', 'string', 'regex:/^\\d+\\s\\d+\\s[a-fA-F0-9]{40,64}$/'];

            case 'DS':
                // DNSSEC Delegation Signer: Key Tag, Algorithm, Digest Type, Digest
                return ['required', 'string', 'regex:/^\\d+\\s\\d+\\s\\d+\\s[a-fA-F0-9]+$/'];

            case 'NAPTR':
                // NAPTR: Order, Preference, Flags, Service, Regexp, Replacement
                return ['required', 'string', 'regex:/^\\d+\\s\\d+\\s\\\"[a-zA-Z0-9]+\\\"\\s\\\"[a-zA-Z0-9]+\\\"\\s\\\".*\\\"\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'RP':
                // Responsible Person: email address (in DNS format) and TXT record pointer
                return ['required', 'string', 'regex:/^([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}\\s([a-zA-Z0-9-]+\\.)+[a-zA-Z]{2,}$/'];

            case 'WKS':
                // Well-Known Services: Protocol, IP, Service Map
                return ['required', 'string']; // Adjust based on specific WKS usage (rarely used)

            default:
                // Default fallback for any unhandled types
                return ['required', 'string'];
        }
    }


    public function indexRecordsApi($uuid)
    {
        // Fetch the API Key from the request
        $apiKey = request()->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Fetch the authenticated user
        $user = Auth::user();

        // Check if the user is authenticated and the API key is valid
        if (!$user || $user->api_token !== $apiKey) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        // Find the zone by UUID
        $zone = Zone::where('uuid', $uuid)->first();

        // Check if the zone exists and if the user has access to it
        if (!$zone || $zone->owner !== $user->id) {
            return response()->json(['error' => 'Zone not found or access denied.'], 404);
        }

        // Fetch the records associated with the zone
        $records = $zone->records;

        // Return the zone details and its records
        return response()->json(['zone' => $zone, 'records' => $records], 200);
    }


    public function updateRecordApi(Request $request, $uuid, $recordId)
    {
        // Fetch the API key from the Authorization header
        $apiKey = $request->header('Authorization');
        $apiKey = str_replace('Bearer ', '', $apiKey);

        // Get the authenticated user
        $user = Auth::user();

        // Check if the API key is valid
        if (!$user || $user->api_token !== $apiKey) {
            return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
        }

        // Find the zone by UUID
        $zone = Zone::where('uuid', $uuid)->first();
        if (!$zone) {
            return response()->json(['error' => 'Zone not found.'], 404);
        }

        // Check if the user owns the zone
        if ($zone->owner !== $user->id) {
            return response()->json(['error' => 'Unauthorized to update records for this zone.'], 403);
        }

        // Find the record by ID in the specified zone
        $record = $zone->records()->find($recordId);
        if (!$record) {
            return response()->json(['error' => 'Record not found in the specified zone.'], 404);
        }

        // Validate the request data
        $validated = $request->validate([
            'host' => ['required', 'string', 'regex:/^([a-zA-Z0-9-]+\\.)*[a-zA-Z0-9-]+$/'],
            'type' => ['required', 'in:A,A6,AAAA,CNAME,DNAME,DS,LOC,MX,NAPTR,NS,PTR,RP,SRV,SSHFP,TXT,WKS'],
            'destination' => $this->getDestinationValidationRule($request->type),
            'valid' => 'required|boolean',
        ]);

        // Update the record in the database
        $record->update([
            'host' => $validated['host'],
            'type' => $validated['type'],
            'destination' => $validated['destination'],
            'valid' => $validated['valid'],
        ]);

        // Update the zone file if required
        $this->updateZoneFile($zone);

        $response = Http::get($this->dns_url.'/reload-coredns');

        return response()->json([
            'message' => 'Record updated successfully.',
            'record' => $record,
        ], 200);
    }

    public function deleteRecordApi(Request $request, $uuid, $recordId)
{
    // Extract API key from the Authorization header
    $apiKey = $request->header('Authorization');
    $apiKey = str_replace('Bearer ', '', $apiKey);

    // Fetch authenticated user
    $user = Auth::user();

    if (!$user || $user->api_token !== $apiKey) {
        return response()->json(['error' => 'Unauthorized. Invalid API Key.'], 401);
    }

    // Find the zone by UUID
    $zone = Zone::where('uuid', $uuid)->first();

    if (!$zone) {
        return response()->json(['error' => 'Zone not found'], 404);
    }

    // Ensure the user has access to the zone
    if ($zone->owner !== $user->id) {
        return response()->json(['error' => 'Unauthorized to delete records for this zone'], 403);
    }

    // Find the specific record by ID
    $record = $zone->records()->where('id', $recordId)->first();

    if (!$record) {
        return response()->json(['error' => 'Record not found'], 404);
    }

    // Prepare the file path for the zone file
    $username = $user->username;
    $zoneDirectory = "/var/www/html/storage/app/coredns/zones/{$username}";
    $zoneFilePath = "{$zoneDirectory}/{$zone->name}.zone";

    // Remove the record from the zone file
    if (file_exists($zoneFilePath)) {
        $lines = file($zoneFilePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $dnsEntry = "{$record->host} IN {$record->type} {$record->destination}";

        if (!empty($record->priority) && $record->type === 'MX') {
            $dnsEntry = "{$record->host} IN {$record->type} {$record->priority} {$record->destination}";
        }

        $filteredLines = array_filter($lines, function ($line) use ($dnsEntry) {
            return trim($line) !== $dnsEntry;
        });

        file_put_contents($zoneFilePath, implode(PHP_EOL, $filteredLines) . PHP_EOL);
    }

    // Delete the record from the database
    $record->delete();

    $response = Http::get($this->dns_url.'/reload-coredns');

    return response()->json(['message' => 'Record deleted successfully'], 200);
}

}
