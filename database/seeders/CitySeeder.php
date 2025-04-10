<?php

namespace Database\Seeders;

use App\Models\Admin\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $cities = [

            ["province_id"=>"2","name"=>"Acturus","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Adylinn","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Airport","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Alexandra Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Amby","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Arcadia","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Ardbennie","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Arlington","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Arundel","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Ascot","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Ashbrittle","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Ashdown Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Aspindale Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Athlone","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Avenues","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Avenues","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Avoca","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Avondale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Avonlea","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Ballantyne Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Banket","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Barbour Fields","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Barham Green","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Beacon Hill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Beatrice","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Beitbridge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Belgravia","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Bellevue","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Belmont","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Belmont Industrial Area","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Belvedere","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Beverly","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Bhazha","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Bikita","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Bindura","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Binga","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Birchenough Bridge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Biriwiri","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Bloomingdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Bonda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Borrowdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Bradfield","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Braeside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Brooke Ridge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Budiriro","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Buf Hill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Buhera","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Bulawayo","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Burnside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Carrick Creag","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Cement","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Centenary","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Chadcombe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Changadzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Chatsworth","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Checheche","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Chegutu","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Chikomba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Chikurubi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Chimanimani","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Chinhoyi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Chipfukutu","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Chipinge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Chiredzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Chishawasha","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Chisipite","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Chitungwiza","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Chivhu","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Chivi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Chiweshe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"City Centre","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"City Centre","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Cold Comfort","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Colne Valley","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Colray","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Concession","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Coronation Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Cotswold Hills","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Cowdray Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Cranebone","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Crowborough","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Damafalls","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Darwendale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Dawn Hill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Domboshava","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Donnington","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Donnington West","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Donny Brooke","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Dorowa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Douglasdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Dzivarasekwa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Eastlea","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Eastview","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Eloana","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Emakhandeni","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Emerald Hill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Emganwini","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Enqameni","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Enqotsheni","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Entumbane","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Epworth","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Esigodini","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Fagadola","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Famona","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Fig Tree","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Filabusi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Filabusi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Fortunes Gate","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Gevstein Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Glaudina","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Glen Lorne","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Glen View","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Glencoe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Glendale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Glengary","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Glennorah","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Glenville","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Glenwood","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Gokwe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Goromonzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Granite Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Graniteside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Green Grove","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Greencroft","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Greendale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Greenhill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Greystone Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Groombridge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Gunhill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Guruve","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Gutu","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Gwabalanda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Gwanda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Gweru","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Haig Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Harare","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Harrisvale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Hatcliffe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Hatfield","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Hauna","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Headlands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Helensvale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Helenvale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Highfield","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Highlands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Highlen","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Highmount","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Hillcrest","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Hillside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Hillside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Hillside South","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Hogerty Hill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Hopley","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Houghton Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Hume Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Hurungwe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Hwange","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Hyde Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Ilanda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Iminyela","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Insiza","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Intini","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Inyathi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Jacaranda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Jerera","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Juliasdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Juru","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Kadoma","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Kambanje","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Kambuzuma","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Kariba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Karoi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Kelvin East","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Kelvin North","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Kelvin West","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Kenilworth","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Kensington","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Kezi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Khumalo","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Khumalo","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Kilallo","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Killarney","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Kingsdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Kopje","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Kutsaga","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Kuwadzana","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Kwekwe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Lakeside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Lalapanzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Letombo Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Lewisam","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Little Norfolk","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Lobengula","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Lobenvale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Lochinvar","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Lochview","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Logan Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Lupane","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Luveve","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mabelreign","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Mabuthweni","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mabvuku","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Madziva","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Magwegwe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Magwegwe North","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Magwegwe West","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Mahatshula","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mainway Meadows","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Makhokhoba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Malindela","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mandara","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Manningdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Manresa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Maphisa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Marange","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Marimba Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Marlands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Marlborough","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Marondera","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Mashava","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Masvingo","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Matidoda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Matsheumhlope","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Matshobana","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Mazowe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Mbalabala","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mbare","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Mberengwa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Meyrick Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Mhangura","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Mhondoro","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Milton Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Mkoba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Monavale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Montgomery","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Montrose","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Morningside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mount Hampden","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mount Pleasant","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Mpopoma","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Msasa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Mt Darwin","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Mt Selinda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Mudzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mufakose","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mukuvisi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Munda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Murambinda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Murewa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Murombedzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Mutare","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Mutasa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Mutawatawa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Mutoko","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Mutorashanga","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Muvonde","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Muzaravani","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Mvuma","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Mvumba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Mvurwi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Mwenezi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Mzilikazi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Mzingwane","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Nayzura","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"New Luveve","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Newlands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Newsmansford","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Newton","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Ngezi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Nguboyenja","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Nhedziwa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Njube","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Nkayi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Nketa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Nkulumane","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Nkwisi Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"North End","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"North Lynne","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"North Trenace","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Northlea","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Northvale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Northwood","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Norton","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Ntaba Moyo","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Ntabazinduna","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Nyabira","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Nyamandlovu","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Nyanga","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Nyanyadzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Nyika","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Odzi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Orange Grove","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Paddonhurst","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Park Meadowlands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Parklands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Parktown","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Parkview","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Penhalonga","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Phelandaba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Philadelphia","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Phumula","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Phumula South","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Plumtree","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Pomona","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Prospect","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Queens Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Queens Park East","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Queens Park West","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Queensdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Queensdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Quinnington","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Rafingora","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Rangemore","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Raylton","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Redcliff","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Redcliff","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"7","name"=>"Renco Mine","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Rhodesville","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Richmond","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Ridgeview","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Riverside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Rockville","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Rolf Valley","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Romney Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Rugare","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"3","name"=>"Rusape","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Rushinga","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Ruwa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Rydle Ridge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Sadza","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Sanyati","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Sauerstown","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Seke,dema","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Selbourne Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Selous","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Sentosa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"4","name"=>"Shamva","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Shangani","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Shawasha Hills","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Sherwood Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Shurugwi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Siabuwa","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Silobela","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Sizinda","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Southdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Southerton","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Southlea Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Southwold","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"St Martins","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"St Marys","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Steeldale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Strathaven","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Suburbs","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Sunningdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Sunninghill","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Sunnyside","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Sunridge","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Sunway City","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Tafara","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Tegela","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"The Grange","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"The Jungle","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Thorngrove","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Trenace","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Tshabalala","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Tshabalala Extension","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Tsholotsho","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Tsholotsho","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Turk Mine","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Tynwald","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Umguza Estate","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Ump","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Umwinsdale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Uplands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Upper Rangemore","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Vainona","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"8","name"=>"Victoria Falls","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"9","name"=>"Wanezi","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Warren Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Waterfalls","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Waterford","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Waterlea","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"5","name"=>"Wedza","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"West Somerton","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Westgate","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Westgate","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Westlea","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Westondale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Westwood","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Willowvale","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Willsgrove","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Windsor Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Woodlands","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Woodville","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"1","name"=>"Woodville Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Workington","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Zhombe","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"2","name"=>"Zimre Park","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"6","name"=>"Zvimba","created_at"=>now(),"updated_at"=>now()],
            ["province_id"=>"10","name"=>"Zvishavane","created_at"=>now(),"updated_at"=>now()],

        ];
        foreach ($cities as $cityData) {
            City::create([
                'province_id' => $cityData['province_id'],
                'name' => $cityData['name'],
            ]);
        }
    }
}
