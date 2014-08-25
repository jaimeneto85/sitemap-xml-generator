sitemap-xml-generator
=====================

<p>Modulo para Magento gerar XML facilmente em canais de marketing.</p>

<p>Sem a necessidade de conhecimento de código ou programação. Você usa apenas variáveis e o módulo faz cálculos, concatena e criar tags. </p>

<p>Usando sempre esse formato: {{codigo_atributo}}</p>

Exemplo:
<p>
<strong>Atributos</strong><br>
Para uma tag &lt;codigo_produto&gt;XXX&lt;/codigo_produto&gt;<br>
Basta preencher no campo Tag: "codigo_produto"<br>
E no campo value: "{{sku}}"<br>
</p>
<p>
<strong>Concatenação</strong><br>
&lt;nome_produto&gt;MINHA LOJA - Produto XXX&lt;/nome_produto&gt;<br>
Tag: "nome_produto"<br>
Value: "MINHA LOJA - {{name}}"<br>
</p>
<p>
<strong>Valores Fixos</strong><br>
&lt;loja_ID&gt;1203948558&lt;/loja_ID&gt;<br>
Tag: "loja_ID"<br>
Value: "1203948558"<br>
</p>
<p>
<strong>Cálculos</strong><br>
&lt;valor_promocional&gt;R$ 50,00&lt;/valor_promocional&gt;<br>
Tag: "valor_promocional"<br>
Value: "{{price}}/2"<br>
</p>

<p>Com ele você poderá gerar XML para Buscapé, Shopping UOL, Zoom, Jacotei e muito mais. Qualquer ferramenta ou sistema que use XML poderá ser facilmente gerenciado por esse módulo.</p>

Desenvolvedores do Projeto
========================
@jaimeneto85 + @wellbishop
