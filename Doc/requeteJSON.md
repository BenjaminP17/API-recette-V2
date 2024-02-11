Créer une recette avec association des ingredients
{
    "name":"Test",
    "picture": null,
    "description":"C'est une nouvelle recette !",
    "cookTime":5,
    "servings":1,
    "idCategory": 3,
    "idIngredient": [1,15,17],
    "step": [
        {
            "instruction": "Allumer le four",
            "priority": 1
        }
    ]
}

Sans association

{
    "name": "Carbonade Flamande",
    "picture": null,
    "description": "La carbonade flamande est un plat traditionnel de la cuisine belge, plus spécifiquement de la région flamande. Il s'agit d'un ragoût de bœuf mijoté lentement dans de la bière, souvent servie avec des pommes de terre ou des frites.",
    "cookTime": 200,
    "servings": 6,
    "category": [
        {
            "name": "Plats"
        }
    ],
    "ingredient": [
        {
            "name": "Oignon",
            "measure": []
        }
    
    ],
    "step": [
        {
            "priority": 1,
            "instruction": "Coupez la viande en morceaux de taille moyenne et assaisonnez-la avec du sel et du poivre."
        },
        {
            "priority": 2,
            "instruction": "Faites chauffer une grande cocotte avec du beurre ou de l'huile à feu moyen. Faites revenir les morceaux de viande jusqu'à ce qu'ils soient bien dorés de tous les côtés. Retirez-les de la cocotte et mettez-les de côté."
        },
        {
            "priority": 3,
            "instruction": "Dans la même cocotte, ajoutez les rondelles d'oignons et faites-les revenir jusqu'à ce qu'elles soient légèrement caramélisées."
        },
        {
            "priority": 4,
            "instruction": "Pendant ce temps, préparez la marinade en mélangeant la bière, le vinaigre, la moutarde, le sucre, le thym et le laurier."
        },
        {
            "priority": 5,
            "instruction": "Remettez la viande dans la cocotte avec les oignons et versez la marinade sur le tout."
        },
        {
            "priority": 6,
            "instruction": "Ajoutez les tranches de pain d'épices sur le dessus. Cela contribuera à épaissir la sauce et à lui donner une saveur caractéristique."
        },
        {
            "priority": 7,
            "instruction": "Portez à ébullition, puis réduisez le feu à doux. Laissez mijoter à feu doux pendant au moins 2 à 3 heures, ou jusqu'à ce que la viande soit tendre."
        }
    ]
}