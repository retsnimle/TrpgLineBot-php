# 骰子狗：開放原始碼的LINE骰子機器人

關於骰子狗
==
骰子狗是一個開放源碼的line骰子機器人計畫，前身是「機器鴨霸獸」。  最早由作者的強者同學（LarryLo）提供基礎原始碼支援。而後幾經開發成為機器鴨霸獸。  

然而此時的機器鴨霸獸是以JavaScript寫成，在外連的支援度以及程式碼的分拆上都遇到困難（其實只是我能力不足）。幾經權衡之後決定以php語言重新寫成，並參考網路上的文章後使用linebot的簡易api（參考資料於文末）。

骰子狗並非想要成為一個全功能的骰子機器人，而是希望成為一個引玉的磚頭。也希望能夠成為對開發Line機器人有興趣的人的一塊拍門磚。  

事實上骰子狗是建立在Heroku的免費伺服器上，其每個月的運轉額度有限，在月底的時候可能會有額度消耗殆盡而終止服務的可能……所以，自己按照下面的教程，客制化做一個自己的LINEBOT吧！
</br></br></br>

試用骰子狗
==
骰子狗的LineID是：@upl5593r  
你也可以使用QR扣：  
![QR](http://i.imgur.com/IpTpfij.png)  

或是點這裡：<a href="https://line.me/R/ti/p/%40upl5593r"><img height="36" border="0" alt="加入好友" src="https://scdn.line-apps.com/n/line_add_friends/btn/zh-Hant.png"></a>
</br></br></br>

特色介紹
==
![特色介紹](http://i.imgur.com/xvcqFtO.jpg)


如何建立自己的Line骰子機器人
==

準備動作：
--
* 先申請好Line帳號（廢話）</br>
* 先申請好Github帳號</br>
* 先申請好Heroku帳號</br>
以下全部選擇用免費的服務就夠了，請不要手殘選到付費。
</br></br></br>

Step1：先把這個專案Fork回去
--
* 到右上角的 ![Fork](http://i.imgur.com/g5VmzkC.jpg) 按鈕嗎，按下去。</br>
把這個專案存到你的Github裡。
</br></br></br></br>

Step2：建立lineBot賬號
--
* 到[https://business.line.me/zh-hant/companies/1253547/services/bot](https://business.line.me/zh-hant/companies/1253547/services/bot)申請一個帳號，</br>
點選「開始使用Messaging API」，按照指示建立你的line賬號。</br>
![開始使用Messaging API](http://i.imgur.com/Zb2Oboy.jpg)</br></br></br>
---

* 當你看到這個畫面的時候，點右邊那個「前往LINE@MANAGER」</br>
![前往LINE@MANAGER](http://i.imgur.com/C2mzamX.jpg)</br></br></br>
---

* 你會看到這個，總之還是點下去</br>
![警告](http://i.imgur.com/XfRa9UU.jpg)</br></br></br>
---
* 照著這個畫面設定</br>
![設定](http://i.imgur.com/PXf10Qs.jpg)</br></br></br>
---
* 接下來移到上面，看到「LINE Developers」了嗎？按下去，然後開著網頁不要關。</br>
![LINE Developers](http://i.imgur.com/aks55p4.jpg)</br></br></br></br>
---


Step3：將LineBot部署到Heroku
--

* 按一下下面這個按鈕</br>
按它→[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)←按它</br></br></br>
---

* 你會看到這個</br>
![Heroku](http://i.imgur.com/sbCVOcW.jpg)</br></br></br>
當然，先取一個App name，那底下兩個要在哪裡找呢，回到上個步驟的LINE Developers網頁</br></br></br></br>




Step4：取得Channel Access Token和Channel Secret
--
* 先取得Channel Secret，按右邊的按鈕</br>
![Channel Secret](http://i.imgur.com/oNN9gUx.jpg)</br>
把取得的字串複製到Step3的LINE_CHANNEL_SECRET</br></br></br>
---
* 再取得Channel Access Token，按右邊的按鈕</br>
![Channel Access Token](http://i.imgur.com/UJ4AQlJ.jpg)</br>
把取得的字串複製到Step3的LINE_CHANNEL_ACCESSTOKEN</br></br>
接著，按下Deploy app，等他跑完之後按下Manage App</br>
距離部署完機器人只差一步啦！
</br></br></br></br>



Step5：鏈接Line與Heroku
--
* 點選settings</br>
![setting](http://i.imgur.com/9fEMoVh.jpg)</br></br></br>
---
* 找到Domains and certificates這個條目，旁邊會有個「Your app can be found at……」加一串網址，把網址複製起來。</br>
![Domain](http://i.imgur.com/dcgyeZa.jpg)</br></br></br>
---
* 回到LINE Developers網頁，選取最底下的edit，找到Webhook URL，把那串網址複製上去，尾巴加上 /LINE/</br>
![webhook](http://i.imgur.com/tn2EN6l.jpg)</br></br></br>
---
* 按下Save。看到在 Webhook URL 旁邊有個 VERIFY 按鈕嗎，按下去。</br>
如果出現 Success. 就表示你成功完成啦！</br>
![Success](http://i.imgur.com/yjlpIh8.jpg)</br></br></br>

如何修改並上傳程式碼咧
==
回到Heroku網頁，點選上面的Deploy，你會看到四種配置程式碼的方法。</br>
![Deploy](http://i.imgur.com/VVRpNLe.jpg)</br>

我猜想如果你是會用第一種（Heroku Git）或是第四種（Container Registry）的人，應該是不會看這種教學文～所以我就不介紹了～</br>
絕、絕對不是我自己也不會的關係哦（眼神漂移）</br>

以第二種（Github）來說的話，你可以綁定你的Github賬號——剛剛我們不是fork了一份程式碼回去嗎？把它連接上去，這樣你就可以在Github那邊修改你要的程式碼，再Deploy過來。</br>
或是你可以使用第三種（Dropbox），當你鏈接之後，它會自動幫你把你剛剛上線的程式碼下載到你的dropbox裡面。你修改完之後再上來Deploy就好咯。</br></br></br>


原始碼解說
==
| 路徑 | 檔名 | 說明 |
| ----- | ----- | ----- |
| .\ | app.json | Heroku的設置文件，LINE_CHANNEL_SECRET 和 LINE_CHANNEL_ACCESSTOKEN的宣告就是在這裡達成的。 |
| .\ | composer.json | php 語言特有的文件。php 語言相當古老且發展相當完整，因此有很多時候可以直接調用函數模組。這個文件就是在做這件事——但本機器人只用了最低限度的LineApi，而且已經包含在程式裡面了。因此這次 composer.json 沒有用到太多。 |
| .\ | composer.lock | 執行 composer 產生的驗證文件。 |
| .\ | README.md | 就是你現在看到的這個說明文件啦！ |
| .\ | .gitattributes | git 的相關文件，解釋起來很複雜，而且這次用不到。不要理他就好。 |

參考資料＆特別感謝
==
Chatbot 開發指南：使用 LINE Bot PHP SDK 打造問答型聊天機器人</br>
[https://www.appcoda.com.tw/line-chatbot-sdk/](https://www.appcoda.com.tw/line-chatbot-sdk/)</br>

使用PHP+Heroku快速打造Linebot回話機器人</br>
[http://www.chy.tw/2017/08/phpherokulinebot.html](http://www.chy.tw/2017/08/phpherokulinebot.html)</br>

感謝強者我同學（LarryLo）在最開始開發鴨霸獸的協助</br>
感謝悠子（victor324）開放的原始碼</br>

願一切榮耀歸于　李孟儒
