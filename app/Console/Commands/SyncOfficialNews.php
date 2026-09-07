<?php

namespace App\Console\Commands;

use App\Models\noticias;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use SimpleXMLElement;

/**
 * Comando para sincronizar noticias oficiales desde feeds RSS/XML de videojuegos.
 *
 * Este proceso evita duplicados por hash o URL y guarda cada noticia como un
 * contenido público sin equipo asociado.
 */
class SyncOfficialNews extends Command
{
    protected $signature = 'news:sync-official {--limit=10 : Maximum items per feed}';

    protected $description = 'Importar noticias recientes desde fuentes oficiales de videojuegos';

    /**
     * Recorre los feeds configurados, valida el XML y guarda las noticias relevantes.
     */
    public function handle(): int
    {
        $imported = 0;
        $limit = max(1, (int) $this->option('limit'));

        foreach (config('official_news.feeds', []) as $feed) {
            try {
                $response = Http::accept('application/rss+xml, application/xml, text/xml')
                    ->timeout(15)
                    ->get($feed['url']);

                if (! $response->successful()) {
                    $this->warn("No se pudo consultar {$feed['name']} ({$response->status()}).");

                    continue;
                }

                $xml = @simplexml_load_string($response->body());
                if (! $xml instanceof SimpleXMLElement) {
                    $this->warn("El feed de {$feed['name']} no tiene un XML válido.");

                    continue;
                }

                foreach ($this->items($xml) as $item) {
                    // Controla el número máximo de elementos importados por corrida.
                    if ($imported >= $limit * count(config('official_news.feeds', []))) {
                        break 2;
                    }

                    // Extrae los campos principales para comparar y guardar la noticia.
                    $url = $this->value($item, 'link');
                    $title = $this->value($item, 'title');
                    $content = $this->value($item, 'description') ?: $this->value($item, 'summary');

                    $sourceHash = $url ? hash('sha256', $url) : null;

                    // Evita duplicados según el hash de la URL o la propia URL del artículo.
                    if (! $url || ! $title || noticias::query()
                        ->where('fuente_hash', $sourceHash)
                        ->orWhere('fuente_url', $url)
                        ->exists()) {
                        continue;
                    }

                    noticias::create([
                        'team_id' => null,
                        'user_id' => null,
                        'titulo' => Str::limit(strip_tags($title), 180, ''),
                        'contenido' => Str::limit(trim(strip_tags($content)), 10000, ''),
                        'categoria' => 'Noticias oficiales',
                        'fuente_nombre' => $feed['name'],
                        'fuente_url' => $url,
                        'fuente_hash' => $sourceHash,
                        'es_oficial' => true,
                    ]);

                    $imported++;
                }
            } catch (\Throwable $exception) {
                $this->warn("Error en {$feed['name']}: {$exception->getMessage()}");
            }
        }

        $this->info("Noticias oficiales importadas: {$imported}.");

        return self::SUCCESS;
    }

    /**
     * Normaliza la estructura XML para obtener un listado de artículos del feed.
     *
     * @return array<int, SimpleXMLElement>
     */
    private function items(SimpleXMLElement $xml): array
    {
        if (isset($xml->channel->item)) {
            return iterator_to_array($xml->channel->item, false);
        }

        return isset($xml->entry)
            ? iterator_to_array($xml->entry, false)
            : [];
    }

    /**
     * Obtiene un campo del item del feed, soportando tanto RSS como Atom.
     */
    private function value(SimpleXMLElement $item, string $field): string
    {
        if ($field === 'link' && isset($item->link['href'])) {
            return trim((string) $item->link['href']);
        }

        return trim((string) ($item->{$field} ?? ''));
    }
}
