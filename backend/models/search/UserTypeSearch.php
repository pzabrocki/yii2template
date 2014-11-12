<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\usertype;

/**
 * UserTypeSearch represents the model behind the search form about `backend\models\usertype`.
 */
class UserTypeSearch extends usertype
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_type_value'], 'integer'],
            [['user_type_name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = usertype::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'user_type_value' => $this->user_type_value,
        ]);

        $query->andFilterWhere(['like', 'user_type_name', $this->user_type_name]);

        return $dataProvider;
    }
}
