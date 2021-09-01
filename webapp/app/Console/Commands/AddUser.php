<?php

namespace App\Console\Commands;

use App\Helpers\ValidationDefaults;
use App\Models\Email;
use App\Models\User;
use App\Perms\AppRoles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AddUser extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add
                            {email : Unique email address}
                            {first_name : First name of the user}
                            {last_name : Last name of the user}
                            {--admin : Make the user administrator}
                            {--password : Set a password to login without email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $email = trim($this->argument('email'));
        $first_name = trim($this->argument('first_name'));
        $last_name = trim($this->argument('last_name'));
        $is_admin = $this->option('admin');
        $set_password = $this->option('password');

        // Validate input
        $validator = Validator::make([
            'email' => $email,
            'first_name' => $first_name,
            'last_name' => $last_name,
        ], [
            'email' => 'required|' . ValidationDefaults::EMAIL . '|unique:email',
            'first_name' => 'required|' . ValidationDefaults::FIRST_NAME,
            'last_name' => 'required|' . ValidationDefaults::LAST_NAME,
        ]);
        if($validator->fails()) {
            $this->error('User creation cancelled failed:');
            foreach ($validator->errors()->all() as $error)
                $this->error($error);
            return 1;
        }

        // Get password to set
        $password = null;
        if($set_password) {
            $password = $this->secret('Enter password');

            // Validate password
            $validator = Validator::make([
                'password' => $password,
            ], [
                'password' => 'required|' . ValidationDefaults::USER_PASSWORD,
            ]);
            if($validator->fails()) {
                $this->error('User creation cancelled failed:');
                foreach ($validator->errors()->all() as $error)
                    $this->error($error);
                return 1;
            }
        }

        // Confirm admin
        if($is_admin && !$this->confirm('Are you sure you want to add this user as administrator?')) {
            $this->error('User creation cancelled');
            return 1;
        }

        // Create a new user
        $user = new User();
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        if($password != null)
            $user->password = Hash::make($password);
        if($is_admin)
            $user->role = AppRoles::ADMIN;
        $user->save();

        // Create the email address
        $user_email = new Email();
        $user_email->user_id = $user->id;
        $user_email->email = $email;
        $user_email->save();

        $this->info('User created!');

        return 0;
    }
}
