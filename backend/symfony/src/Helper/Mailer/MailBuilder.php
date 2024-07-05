<?php

namespace App\Helper\Mailer;

class MailBuilder
{
    /**
     * @param string $content
     * @return string
     */
    public function buildContent(string $content): string
    {
        return $this->getHeader() . $content . $this->getFooter();
    }

    /**
     * @return string
     */
    private function getHeader(): string
    {
        return 'Dobrý den, <br><br>';
    }

    /**
     * @return string
     */
    private function getFooter(): string {
        return '<br><br>S pozdravem, <br> SoukromeKino';
    }
}
