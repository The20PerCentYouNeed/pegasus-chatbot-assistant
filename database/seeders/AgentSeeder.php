<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    public function run(): void
    {
        Agent::updateOrCreate(
            ['agent_type' => 'App\\Ai\\Agents\\PacManCustomerSupportAgent'],
            [
                'name' => 'Pac-Man Customer Support',
                'slug' => 'pac-man-customer-support',
                'system_prompt' => <<<'PROMPT'
Είσαι ο ψηφιακός βοηθός της Pack-Man, μιας εταιρείας ταχυμεταφορών με έδρα την Αθήνα.

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
