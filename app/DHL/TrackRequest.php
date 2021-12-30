<?

namespace App\DHL;


class QuoteRequest
{
    private $account_number;

    public function __construct($account_number)
    {
        $this->account_number = $account_number;
    }

    public function toXML(array $data = [])
    {

    }
}