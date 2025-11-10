<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Mappa nome categoria -> id (assicurati che il CategoriesSeeder sia già stato eseguito)
            $catIds = Category::pluck('id', 'name')->map(fn ($id) => (int) $id)->toArray();

            // Percorso immagine di default (public/images/default_product.jpg)
            $defaultPhoto = 'images/default_product.jpg';

            // 👇 Catalogo prodotti (nome, categoria, descrizione, prezzo/kg, photo)
            $products = [

                // =========================
                // CARNI BIANCHE / HAMBURGER
                // =========================
                [
                    'name'        => 'Hamburger di pollo',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo 100% carne bianca, macinata fresca e condita con aromi naturali.',
                    'price_per_kg'=> 8.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e formaggio',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con cuore filante di formaggio.',
                    'price_per_kg'=> 9.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e limone',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo marinato al limone, fresco e profumato.',
                    'price_per_kg'=> 9.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e ortolana',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con verdure miste saltate (ortolana).',
                    'price_per_kg'=> 9.79, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e melanzane',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con melanzane a cubetti.',
                    'price_per_kg'=> 9.79, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e zucchine',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con zucchine fresche.',
                    'price_per_kg'=> 9.79, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e peperoni',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con peperoni dolci.',
                    'price_per_kg'=> 9.79, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e carciofi',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con carciofi e note erbacee.',
                    'price_per_kg'=> 10.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e cipolla',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con cipolla dolce.',
                    'price_per_kg'=> 9.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo, rucola e parmigiano',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con rucola fresca e scaglie di parmigiano.',
                    'price_per_kg'=> 10.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo, speck e noci',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con speck e granella di noci.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger pollo e spinaci',
                    'category'    => 'Carni bianche',
                    'description' => 'Hamburger di pollo con spinaci finemente tritati.',
                    'price_per_kg'=> 9.79, 'photo' => $defaultPhoto,
                ],

                // ==========
                // POLPETTE
                // ==========
                [
                    'name'        => 'Polpette di pollo e formaggio',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, fior di latte, formaggio Galbani, pangrattato.',
                    'price_per_kg'=> 11.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette pollo, speck e provola',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, parmigiano, speck, provola, formaggio, sale.',
                    'price_per_kg'=> 11.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette limone e fior di latte',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, parmigiano, limone e fior di latte.',
                    'price_per_kg'=> 11.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette pringles e cheddar',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, cheddar e croccantezza Pringles.',
                    'price_per_kg'=> 11.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette melanzane a funghetto, provola e pesto',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, parmigiano, melanzane al pomodoro, pesto di basilico, provola.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette mortadella e pistacchio',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, parmigiano, mortadella, pesto di pistacchio, formaggio Galbani.',
                    'price_per_kg'=> 12.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette carciofi e provola',
                    'category'    => 'Polpette',
                    'description' => 'Pollo, uova, parmigiano, carciofi, provola e purè di patate.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],

                // ============
                // MORBIDELLE
                // ============
                [
                    'name'        => 'Spinacine',
                    'category'    => 'Morbidelle',
                    'description' => 'Pollo, uova, parmigiano, spinaci. Morbide e gustose.',
                    'price_per_kg'=> 10.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Morbidelle al formaggio',
                    'category'    => 'Morbidelle',
                    'description' => 'Pollo, uova, parmigiano, formaggio.',
                    'price_per_kg'=> 10.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Morbidelle cotto e provola',
                    'category'    => 'Morbidelle',
                    'description' => 'Pollo, uova, parmigiano, prosciutto cotto e provola.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Morbidelle melanzane e provola',
                    'category'    => 'Morbidelle',
                    'description' => 'Pollo, uova, parmigiano, melanzane e provola.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Morbidelle con carciofi',
                    'category'    => 'Morbidelle',
                    'description' => 'Pollo, uova, parmigiano, carciofi.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],

                // =======================
                // SALSICCE / PANATI & PRONTI
                // =======================
                [
                    'name'        => 'Salsicce di pollo e tacchino',
                    'category'    => 'Salsicce',
                    'description' => 'Salsicce leggere di pollo e tacchino (sale, pepe).',
                    'price_per_kg'=> 9.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Crocche di pollo con taralli e provola',
                    'category'    => 'Panati & pronti',
                    'description' => 'Pollo, uova, parmigiano, provola, taralli.',
                    'price_per_kg'=> 11.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Cotolette di pollo',
                    'category'    => 'Panati & pronti',
                    'description' => 'Petto di pollo, uova e pangrattato: classiche e croccanti.',
                    'price_per_kg'=> 11.49, 'photo' => $defaultPhoto,
                ],

                // ====================
                // SPIEDINI & GRIGLIATE
                // ====================
                [
                    'name'        => 'Spiedini misti',
                    'category'    => 'Spiedini & grigliate',
                    'description' => 'Spiedini con pollo, maiale, tacchino e peperoni.',
                    'price_per_kg'=> 12.99, 'photo' => $defaultPhoto,
                ],

                // ==================
                // SPECIALITÀ POLLO
                // ==================
                [
                    'name'        => 'Boscaiola di pollo e tacchino',
                    'category'    => 'Specialità pollo',
                    'description' => 'Pollo e tacchino con funghi, piselli e carote.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Tagliata di petto di pollo limone e finocchi',
                    'category'    => 'Specialità pollo',
                    'description' => 'Tagliata leggera di petto di pollo con limone e finocchi.',
                    'price_per_kg'=> 12.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Straccetti di pollo corn flakes',
                    'category'    => 'Specialità pollo',
                    'description' => 'Straccetti croccanti con panatura ai corn flakes.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Straccetti di pollo BBQ e paprika dolce',
                    'category'    => 'Specialità pollo',
                    'description' => 'Straccetti glassati alla salsa barbecue e paprika dolce.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Bocconcini di pollo salsa di soia e mandorle',
                    'category'    => 'Specialità pollo',
                    'description' => 'Bocconcini sfiziosi con salsa di soia e mandorle.',
                    'price_per_kg'=> 12.99, 'photo' => $defaultPhoto,
                ],

                // =========================
                // NUGGETS & FINGER FOOD
                // =========================
                [
                    'name'        => 'Nuggets di pollo',
                    'category'    => 'Nuggets & Finger food',
                    'description' => 'Nuggets di pollo con aromi naturali.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],

                // =================
                // PIADINE & SFIZI
                // =================
                [
                    'name'        => 'Piadina con tagliata di pollo',
                    'category'    => 'Piadine & Sfizi',
                    'description' => 'Tagliata di pollo Amadori, rucola, formaggio e pancetta in piadina.',
                    'price_per_kg'=> 13.49, 'photo' => $defaultPhoto,
                ],

                // ======
                // MAIALE
                // ======
                [
                    'name'        => 'Hamburger di maiale friarielli e mozzarella',
                    'category'    => 'Maiale',
                    'description' => 'Hamburger di maiale con friarielli e mozzarella.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger di maiale speck e provola',
                    'category'    => 'Maiale',
                    'description' => 'Hamburger di maiale con speck e provola.',
                    'price_per_kg'=> 10.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Salsicce di maiale al finocchietto',
                    'category'    => 'Maiale',
                    'description' => 'Salsicce tradizionali al finocchietto.',
                    'price_per_kg'=> 9.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Salsicce di maiale senza finocchietto',
                    'category'    => 'Maiale',
                    'description' => 'Salsicce di maiale (sale, pepe).',
                    'price_per_kg'=> 9.79, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Capicollo di maiale',
                    'category'    => 'Maiale',
                    'description' => 'Capicollo di maiale selezionato.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Prosciutto di maiale',
                    'category'    => 'Maiale',
                    'description' => 'Tagli di prosciutto di maiale per arrosti o cotture lente.',
                    'price_per_kg'=> 11.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Arista di maiale',
                    'category'    => 'Maiale',
                    'description' => 'Arista di maiale ideale per arrosti.',
                    'price_per_kg'=> 11.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Costolette di maiale',
                    'category'    => 'Maiale',
                    'description' => 'Costolette saporite da grigliare.',
                    'price_per_kg'=> 11.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette di maiale con friarielli, purè e provola',
                    'category'    => 'Maiale',
                    'description' => 'Polpette di maiale con friarielli, purè di patate e provola.',
                    'price_per_kg'=> 12.49, 'photo' => $defaultPhoto,
                ],

                // ======
                // BOVINO
                // ======
                [
                    'name'        => 'Colardella',
                    'category'    => 'Bovino',
                    'description' => 'Taglio di bovino per cotture lente e sugosi.',
                    'price_per_kg'=> 14.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Nove',
                    'category'    => 'Bovino',
                    'description' => 'Taglio bovino selezionato (locale).',
                    'price_per_kg'=> 14.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Girello',
                    'category'    => 'Bovino',
                    'description' => 'Girello di bovino magro, ideale per roast beef.',
                    'price_per_kg'=> 15.49, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Natica',
                    'category'    => 'Bovino',
                    'description' => 'Taglio posteriore del bovino, adatto ad arrosti e fettine.',
                    'price_per_kg'=> 14.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Cotolette di bovino',
                    'category'    => 'Bovino',
                    'description' => 'Fettine di bovino panate.',
                    'price_per_kg'=> 15.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Picanha',
                    'category'    => 'Bovino',
                    'description' => 'Classico taglio brasiliano, ottimo alla griglia.',
                    'price_per_kg'=> 18.99, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Entrecôte',
                    'category'    => 'Bovino',
                    'description' => 'Taglio nobile e saporito, perfetto alla piastra.',
                    'price_per_kg'=> 22.90, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Arrosto di costata',
                    'category'    => 'Bovino',
                    'description' => 'Costata preparata per arrosti.',
                    'price_per_kg'=> 21.50, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Beef (taglio selezionato)',
                    'category'    => 'Bovino',
                    'description' => 'Selezione di manzo per griglia e padella.',
                    'price_per_kg'=> 19.90, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Hamburger di scottona',
                    'category'    => 'Bovino',
                    'description' => 'Hamburger di scottona, succoso e tenero.',
                    'price_per_kg'=> 16.90, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Girelle alla Nerano',
                    'category'    => 'Bovino',
                    'description' => 'Carne, uova, parmigiano, zucchine, provolone del Monaco, galbanone.',
                    'price_per_kg'=> 17.90, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Girelle ricotta e spinaci',
                    'category'    => 'Bovino',
                    'description' => 'Carne, uova, parmigiano, ricotta e spinaci.',
                    'price_per_kg'=> 17.50, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Tramezzini cotto e galbanone',
                    'category'    => 'Bovino',
                    'description' => 'Carne, uova, parmigiano, prosciutto cotto e galbanone.',
                    'price_per_kg'=> 16.50, 'photo' => $defaultPhoto,
                ],
                [
                    'name'        => 'Polpette classiche di bovino',
                    'category'    => 'Bovino',
                    'description' => 'Uova, sale, pepe, parmigiano e mollica di pane.',
                    'price_per_kg'=> 14.90, 'photo' => $defaultPhoto,
                ],
            ];

            // Inserimento/aggiornamento idempotente
            foreach ($products as $p) {
                // id categoria da nome
                $categoryName = $p['category'];
                if (!isset($catIds[$categoryName])) {
                    // se la categoria non esiste, la creo al volo per robustezza
                    $newCat = Category::firstOrCreate(['name' => $categoryName], ['name' => $categoryName]);
                    $catIds[$categoryName] = $newCat->id;
                }

                Product::updateOrCreate(
                    ['name' => $p['name']], // chiave di unicità logica
                    [
                        'description'  => $p['description'],
                        'photo'        => $p['photo'],
                        'category_id'  => $catIds[$categoryName],
                        'price_per_kg' => $p['price_per_kg'],
                        'is_active'    => true,
                    ]
                );
            }
        });
    }
}
