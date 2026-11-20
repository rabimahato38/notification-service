<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use App\Models\WhatsAppLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    private function log(array $overrides = []): WhatsAppLog
    {
        $createdAt = $overrides['created_at'] ?? null;
        unset($overrides['created_at']);

        $log = WhatsAppLog::create(array_merge([
            'service_id' => 'SRV-AAAA1111',
            'service_name' => 'Billing',
            'to' => '9876****10',
            'template' => 'invoice_ready',
            'language' => 'en',
            'parameters' => ['John'],
            'response' => ['messages' => [['id' => 'wamid.1']]],
            'status_code' => 200,
            'status' => 'success',
        ], $overrides));

        if ($createdAt) {
            $log->forceFill(['created_at' => $createdAt])->save();
        }

        return $log;
    }

    public function test_guests_are_sent_to_login(): void
    {
        foreach (['/', '/services', '/logs', '/logs/export'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_dashboard_shows_empty_state_without_traffic(): void
    {
        $this->actingAs($this->admin)->get('/')
            ->assertOk()
            ->assertSee('No messages yet')
            ->assertSee('Messages per day');
    }

    public function test_dashboard_counts_and_success_rate(): void
    {
        $this->log();
        $this->log();
        $this->log();
        $this->log(['status' => 'failed', 'status_code' => 502, 'template' => 'otp_code']);
        $this->log(['created_at' => Carbon::now()->subDays(20)]); // outside every window

        $response = $this->actingAs($this->admin)->get('/');

        $response->assertOk()
            ->assertViewHas('stats', fn ($s) => $s['today'] === 4
                && $s['week_total'] === 4
                && $s['success_rate'] === 75.0
                && $s['failed_24h'] === 1)
            ->assertViewHas('daily', fn ($d) => count($d) === 14 && end($d)['ok'] === 3 && end($d)['failed'] === 1)
            ->assertSee('invoice_ready')
            ->assertSee('View failures');
    }

    public function test_log_list_filters_by_status_and_service(): void
    {
        $this->log(['template' => 'delivered_tpl']);
        $this->log(['status' => 'failed', 'status_code' => 504, 'template' => 'failed_tpl']);
        $this->log(['service_name' => 'Other', 'template' => 'other_tpl']);

        $this->actingAs($this->admin)->get('/logs?status=failed')
            ->assertOk()
            ->assertViewHas('logs', fn ($p) => $p->total() === 1 && $p->first()->template === 'failed_tpl');

        $this->actingAs($this->admin)->get('/logs?service_name=Other')
            ->assertViewHas('logs', fn ($p) => $p->total() === 1 && $p->first()->template === 'other_tpl');
    }

    public function test_log_filters_reject_bad_input(): void
    {
        $this->actingAs($this->admin)->get('/logs?status=maybe')->assertSessionHasErrors('status');
    }

    public function test_log_detail_shows_provider_response(): void
    {
        $log = $this->log();

        $this->actingAs($this->admin)->get("/logs/{$log->id}")
            ->assertOk()
            ->assertSee('wamid.1')
            ->assertSee('invoice_ready');
    }

    public function test_csv_export_respects_filters_and_neutralises_formulas(): void
    {
        $this->log(['template' => '=HYPERLINK("x")']);
        $this->log(['status' => 'failed', 'status_code' => 502, 'template' => 'not_exported']);

        $csv = $this->actingAs($this->admin)->get('/logs/export?status=success')
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->streamedContent();

        $this->assertStringContainsString("'=HYPERLINK", $csv);
        $this->assertStringNotContainsString('not_exported', $csv);
    }

    public function test_service_can_be_created_with_template_list(): void
    {
        $this->actingAs($this->admin)->post('/services', [
            'service_name' => 'Billing',
            'allowed_templates' => ' a, b ,, a ',
        ])->assertRedirect('/services');

        $this->assertSame(['a', 'b'], Service::first()->allowed_templates);
    }

    public function test_service_can_be_edited(): void
    {
        $service = Service::create(['service_name' => 'Old', 'allowed_templates' => ['x']]);

        $this->actingAs($this->admin)->put("/services/{$service->id}", [
            'service_name' => 'New name',
            'description' => 'Used by the billing app',
            'allowed_templates' => '',
        ])->assertRedirect('/services');

        $service->refresh();
        $this->assertSame('New name', $service->service_name);
        $this->assertSame('Used by the billing app', $service->description);
        $this->assertNull($service->allowed_templates);
    }

    public function test_edit_errors_go_to_the_services_own_bag(): void
    {
        $service = Service::create(['service_name' => 'Keep']);

        $this->actingAs($this->admin)->from('/services')->put("/services/{$service->id}", ['service_name' => ''])
            ->assertRedirect('/services')
            ->assertSessionHasErrors('service_name', null, 'edit_'.$service->id);

        $this->assertSame('Keep', $service->fresh()->service_name);
    }

    public function test_services_page_search_and_usage_counts(): void
    {
        $billing = Service::create(['service_name' => 'Billing']);
        Service::create(['service_name' => 'Marketing']);
        $this->log(['service_id' => $billing->service_id]);
        $this->log(['service_id' => $billing->service_id, 'status' => 'failed', 'status_code' => 502]);

        $this->actingAs($this->admin)->get('/services?q=bill')
            ->assertOk()
            ->assertViewHas('services', fn ($s) => $s->count() === 1
                && $s->first()->messages_7d === 2
                && $s->first()->failed_7d === 1)
            ->assertDontSee('Marketing');
    }

    public function test_api_key_is_masked_by_default(): void
    {
        $service = Service::create(['service_name' => 'Billing']);

        $html = $this->actingAs($this->admin)->get('/services')->getContent();

        $this->assertStringContainsString($service->maskedApiKey(), $html);
        $this->assertStringNotContainsString('>'.$service->api_key.'<', $html);
    }

    public function test_service_names_cannot_inject_script(): void
    {
        Service::create(['service_name' => "x');alert(1);//<script>bad()</script>"]);

        $html = $this->actingAs($this->admin)->get('/services')->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>bad()', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('onsubmit=', $html);
    }
}
