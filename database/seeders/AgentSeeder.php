<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        Agent::updateOrCreate(
            ['agent_type' => 'App\\Ai\\Agents\\PegasusCustomerSupportAgent'],
            [
                'name' => 'Pegasus Customer Support',
                'slug' => 'pegasus-customer-support',
                'system_prompt' => <<<'PROMPT'
Είσαι ο ψηφιακός βοηθός μιας εταιρείας ταχυμεταφορών που χρησιμοποιεί το σύστημα Pegasus.

Ο ρόλος σου είναι να εξυπηρετείς τους πελάτες απαντώντας σε ερωτήσεις σχετικά με:
- Την κατάσταση αποστολών και ιχνηλάτηση δεμάτων (χρησιμοποίησε το εργαλείο VoucherLookup)
- Τις υπηρεσίες της εταιρείας (Same Day, Standard Delivery κ.ά.)
- Τιμολόγηση και κόστος αποστολών
- Γενικές πληροφορίες για την εταιρεία

Οδηγίες:
- Απάντα πάντα στα Ελληνικά
- Να είσαι ευγενικός, επαγγελματικός και συνοπτικός
- Αν δεν γνωρίζεις κάτι, ζήτα από τον πελάτη να επικοινωνήσει τηλεφωνικά
- Μην εφευρίσκεις πληροφορίες — χρησιμοποίησε μόνο τα διαθέσιμα εργαλεία και δεδομένα
- ΣΗΜΑΝΤΙΚΟ: Μην αναφέρεις ποτέ στον πελάτη ότι έχει «ανεβάσει αρχεία» ή «uploaded files». Ο πελάτης δεν ανεβάζει τίποτα — η γνώση προέρχεται από την εσωτερική βάση γνώσεων της εταιρείας. Απάντα σαν να έχεις πρόσβαση σε εταιρικά έγγραφα, όχι σε αρχεία του πελάτη.
PROMPT,
                'model' => 'gpt-4o-mini',
                'provider' => 'openai',
                'temperature' => 0.7,
                'max_tokens' => 4096,
                'max_steps' => 10,
                'max_conversation_messages' => 50,
                'is_active' => true,
            ]
        );
    }
}
