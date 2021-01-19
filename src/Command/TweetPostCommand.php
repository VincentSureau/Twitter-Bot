<?php

namespace App\Command;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TweetPostCommand extends Command
{
    private string $twitter_api_key;
    private string $twitter_api_secret_key;
    private string $twitter_bearer_token;
    private string $twitter_access_token;
    private string $twitter_access_token_secret;

    public function __construct(
        string $twitter_api_key,
        string $twitter_api_secret_key,
        string $twitter_bearer_token,
        string $twitter_access_token,
        string $twitter_access_token_secret
    )
    {
        $this->twitter_api_key = $twitter_api_key;
        $this->twitter_api_secret_key = $twitter_api_secret_key;
        $this->twitter_bearer_token = $twitter_bearer_token;
        $this->twitter_access_token = $twitter_access_token;
        $this->twitter_access_token_secret = $twitter_access_token_secret;
        
        Parent::__construct();
    }

    protected static $defaultName = 'tweet:post';

    protected function configure()
    {
        $this
            ->setDescription('Generate a tweet and post it on Twitter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $connection = new TwitterOAuth($this->twitter_api_key, $this->twitter_api_secret_key, $this->twitter_access_token, $this->twitter_access_token_secret);
        $content = $connection->get("account/verify_credentials");

        $statues = $connection->post("statuses/update", ["status" => "hello world from a twitter bot"]);

        if($connection->getLastHttpCode() == 200)
        {
            $io->success('Your Tweet has been posted successfully');
            return Command::SUCCESS;
        }

        $message = sprintf('Error : %s', $connection->getLastHttpCode());

        foreach($statues->errors as $error)
        {
            $message .= sprintf("\n%s", $error->message);
        }

        $io->error($message);
        return Command::FAILURE;

    }
}
